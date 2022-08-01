<?php

namespace Farsh4d\Bank\Drivers;


use Illuminate\Support\Facades\View;

class Mellat extends AbstractDriver
{
    protected $baseUrl = 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl';

    /**
     * @return AbstractDriver
     * @throws \Exception
     */
    public function ready()
    {
        $this->intitReady();
        
        $params = [
            'terminalId' => $this->config['terminalNumber'],
            'userName' => $this->config['username'],
            'userPassword' => $this->config['password'],
            'orderId' => $this->orderId,
            'amount' => $this->price,
            'localDate' => verta()->format('Ymd'),
            'localTime' => verta()->format('His'),
            'additionalData' => '',
            'callBackUrl' => $this->callback,
            'payerId' => 0,
        ];

        $response = explode(',', $this->sendSoapRequest('bpPayRequest', $params));
        $this->exceptionHandle($response[0], $response[1] ?? '');
        $this->refId = $response[1];
        
        $this->updateTransactionRefId();
        
        return $this;
    }

    private function sendSoapRequest($service, $params)
    {
        $soap = $this->createSoapClient();
        return $soap->{$service}($params)->return;
    }

    private function createSoapClient()
    {
        if (array_key_exists('proxy', $this->config)) {
            if (isset($this->config['proxy']['host'], $this->config['proxy']['port'])) {
                return new \SoapClient($this->base_url, [
                    'proxy_port' => $this->config['proxy']['port'],
                    'proxy_host' => $this->config['proxy']['host'],
                ]);
            }
        }

        return new \SoapClient($this->baseUrl);
    }

    private function exceptionHandle($responseStatus, $msg = '')
    {
        if ((int)$responseStatus !== 0) {
            if (!is_null($this->transaction)) {
                $this->transactionfailed([
                    'responseStatus' => $responseStatus,
                    'msg' => $msg
                ]);
            }
            throw new \Exception($msg, $responseStatus);
        }
    }

    public function redirect()
    {
        return View::make('bank::mellat-redirector')->with([
            'refId' => $this->refId
        ]);
    }

    public function verify($transaction)
    {
        if ((int)request()->get('ResCode') !== 0) {
            $this->transactionfailed([
                'responseStatus' => (int)request()->get('ResCode'),
                'msg' => 'Transaction Failed!'
            ]);
            throw new \Exception();
        }

        $params = [
            'terminalId' => $this->config['terminalNumber'],
            'userName' => $this->config['username'],
            'userPassword' => $this->config['password'],
            'orderId' => $transaction->order_id,
            'saleOrderId' => $transaction->order_id,
            'saleReferenceId' => request()->get('SaleReferenceId')
        ];

        $response = $this->sendSoapRequest('bpVerifyRequest', $params);
        try {
            $this->exceptionHandle($response);
        } catch (\Exception $exception) {
            $this->sendSoapRequest('bpReversalRequest', $params);
            throw new \Exception();
        }

        $settleResponse = (int)$this->sendSoapRequest('bpSettleRequest', $params);
        if ($settleResponse !== 0 && $settleResponse !== 45) {
            $this->transactionfailed([
                'responseStatus' => $settleResponse,
                'msg' => 'Transaction Failed!'
            ]);
            
            $this->sendSoapRequest('bpReversalRequest', $params);
            throw new \Exception();
        }
        
        $this->transactionSucceeded(request()->all());
    }
}
