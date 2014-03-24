<html>
<head>
<title>Buku Tamu Sederhana</title>
</head>
<body>
<h2>Buku Tamu Sederhana</h2>
<h3>Isi Buku Tamu</h3>
<form method="POST" action="<?php echo this()->base_url;?>/bukutamu/page/simpan">
<p><input type="text" name="nama" size="40" value="<?php echo $nama;?>" /><label>Nama</label></p>
<p><input type="text" name="email" size="40" value="<?php echo $email;?>" /><label>Email</label></p>
<label>Isi</label>
<p><textarea name="isi" rows="5" cols="40"><?php echo $isi;?></textarea></p>
<input type="hidden" name ="id" value="<?php echo $id;?>" />
<input type="hidden" name ="status" value="<?php echo $status;?>" />
<p><input type="submit" value="simpan"></p>
</form>
<h3>Daftar Buku Tamu</h3>
<table border="1" width="100%" style="border-collapse:collapse;">
<tr>
	<th>ID</th>
	<th>Nama</th>
	<th>Email</th>
	<th>Isi</th>
	<th>Tgl Posting</th>
	<th>Operasi</th>
</tr>
<?php 
foreach($daftar_bukutamu as $d) {
?>
<tr>
	<td><?php echo $d['id'];?></td>
	<td><?php echo $d['nama'];?></td>
	<td><?php echo $d['email'];?></td>
	<td><?php echo $d['isi'];?></td>
	<td><?php echo $d['tgl_posting'];?></td>
	<td align="center">
	<a href="<?php echo this()->base_url.'/bukutamu/page/edit/'.$d['id'];?>">Edit</a> - 
	<a href="<?php echo this()->base_url.'/bukutamu/page/hapus/'.$d['id'];?>" onclick="return confirm('Anda yakin akan menghapus data ini?')">Hapus</a>
	</td>
</tr>
<?php
}//end foreach
?>
</table>
<p>Waktu komputasi {elapsed_time} detik. Kebutuhan memory {memory_usage}</p>
</body>
</html>

