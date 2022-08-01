<html>
<head>
    <title>در حال انتقال به صفحه پرداخت</title>
</head>
<body>
<form id="redirect_to_psp" method="post" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat">
    <input type="hidden" id="name" name="RefId" value="{{ $refId }}" />
    <input type="submit" id="submit_btn" value="{{ __('app.start_payment') }}">
</form>
<script language="JavaScript" type="text/javascript">
    document.getElementById("submit_btn").style.display = "none";
    document.getElementById("redirect_to_psp").submit();
</script>
</body>
</html>
