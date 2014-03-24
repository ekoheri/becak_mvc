<?php defined('SYS') or exit('Access Denied!');
class page extends controller
{
	public function __construct()
	{
		parent::__construct();
		loader::model('guestbook_model');
	}
	public function index()
	{
		$this->output->cache(1);		
		$data = array(
			'id' => '',
			'nama' => '',
			'email' => '',
			'isi' => '',
			'tgl_posting' => '',
			'status' => 'data_baru',
			'daftar_bukutamu' => $this->guestbook_model->GetData()
		);
		echo view("guestbook_view", $data, false);
	}
	function edit($id){
		$data_lama = $this->guestbook_model->GetData("WHERE id=$id");
		$temp = array();
		foreach($data_lama as $key => $val){
			$temp[$key] = $val;
		}
		$data = array(
			'id' => $id,
			'nama' => $temp[0]['nama'],
			'email' => $temp[0]['email'],
			'isi' => $temp[0]['isi'],
			'tgl_posting' => $temp[0]['tgl_posting'],
			'status' => 'edit',
			'daftar_bukutamu' => $this->guestbook_model->GetData()
		);
		echo view("guestbook_view", $data, false);
	}
	function simpan()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : NULL;		
		$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$isi = isset($_POST['isi']) ? $_POST['isi'] : '';
		$status = isset($_POST['status']) ? $_POST['status'] : 'data_baru';
		
		$data = array(
			'id' => $id,
			'nama' => $nama,
			'email' => $email,
			'isi' => $isi,
			'tgl_posting' => date('Y-m-d H:i:s')
		);
		if($status == 'data_baru'){
			$result = $this->guestbook_model->InsertData($data);
			if($result==1) {
				echo "<h2>Sukses Menambah Buku Tamu</h2>";
				echo "<p><a href='".this()->base_url."/bukutamu/page'>Kembali ke halaman depan</a></p>";
			}
		} else if($status == 'edit'){
			$result = $this->guestbook_model->UpdateData($data, array('id'=> $id));
			if($result==1) {
				echo "<h2>Sukses Merubah Buku Tamunya $nama</h2>";
				echo "<p><a href='".this()->base_url."/bukutamu/page'>Kembali ke halaman depan</a></p>";
			}
		}//end if edit
		
	}//end simpan
	function hapus($id){
		$result = $this->guestbook_model->DeleteData(array('id'=> $id));
		if($result==1) {
			echo "<h2>Sukses Menghapus Buku Tamu</h2>";
			echo "<p><a href='".this()->base_url."/bukutamu/page'>Kembali ke halaman depan</a></p>";
		}
	}
}
?>
