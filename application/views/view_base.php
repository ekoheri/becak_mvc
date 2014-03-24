<html>
<head>
<?php echo this()->meta; ?>

<title><?php echo this()->title; ?></title>

<base href="<?php echo this()->base; ?>" />

<?php 
this()->head .= css('welcome.css');
echo this()->head;
?>

</head>

<body>
<h1>Welcome to Becak MVC Framework</h1>
<?php echo this()->body; ?>

<p>Page rendered in {elapsed_time} seconds using {memory_usage} of memory</p>
</body>

</html>
