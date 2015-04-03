<?php
namespace Application\Controller;

use FrameworkMvc\Mvc\Controller;
use ProvaTrabalho\Model\ProvaTrabalho;

class HomeController extends Controller{
    private $viewData;
    private $uploadDir = "uploads/";
    
    public function indexAction(){
        return array();
    }
    
    public function searchAction(){
        $this->viewData = array();
        $search = "";
        if(isset($_POST['search'])){
            $search = $_POST['search'];
        }
        $this->viewData['search'] = $search;
        return $this->viewData;
    }
    
    public function uploadAction(){
        $this->viewData = array();
        //Array que guarda todas os arquivos enviados com sucesso
        $uploadedFiles = array();
        if(!empty($_POST)){
            //Reorganiza o array de arquivos
            $files = $this->reorganizeArrayFiles($_FILES['file']);
            echo "<script type='text/javascript'>localStorage.clear()</script>";

            foreach($files as $file){
                if($file['error'] == 0){
                    $tmpFile = $file['tmp_name'];
                    $nome = $file['name'];
                    //Extensão do arquivo
                    $extensao = strrchr($nome, '.');
                    $extensao = strtolower($extensao);
                    
                    //Checa se é um arquivo com extensão válida (talvez seja melhor checar pelo type)
                    if(strstr('.jpg;.jpeg;.gif;.png;.pdf', $extensao))
                    {
                        // Cria um nome único para esta imagem
                        // Evita que duplique as imagens no servidor.
                        $novoNome = md5(microtime()) . $extensao;
                         
                        // Concatena a pasta com o nome
                        $destino = $this->uploadDir.$novoNome;
                        // tenta mover o arquivo para o destino
                        if(move_uploaded_file($tmpFile, $destino))
                        {
                            //echo "<img src=\"" . $destino . "\" />";
                            $arquivo = new ProvaTrabalho();
                            $arquivo->setArquivo($destino);
                            $arquivo->setImage(($extensao != ".pdf"));
                            $arquivo->setNome($nome);
                            $uploadedFiles[] = $arquivo;
                        }
                        else{
                            echo "<pre> ${print_r($file)} </pre>";
                            echo "<h1>Erro ao salvar o arquivo</h1>";
                        }
                    }
                }
            }
            $this->viewData['files'] = $uploadedFiles;
        }
        return $this->viewData;
    }
    
    public function saveAction(){
        $this->viewData = array();
        if(!empty($_POST)){
            echo "<pre> ${print_r($_POST)} </pre>";
            $this->viewData['post'] = $_POST;
        }
        //header("location:?module=application&controller=home&action=index");
        return $this->viewData;
    }
    
    /**
     * Reorganiza o array de arquivos para um padrão mais claro
     * 
     * A configuração final do array fica:
     * Array
     * (
     *      [0] => Array
     *      (
     *          [name] => arquivo.txt
     *          [type] => text/plain
     *          [tmp_name] => /tmp/as313as
     *      )
     *      [1] => Array
     *      (
     *          [name] => foto.png
     *          [type] => image/png
     *          [tmp_name] => /tmp/qwe41as
     *      )
     * )
     * @param array $file_post Array de arquivos
     * @return array Array ordenado de forma mais clara
     */
    private function reorganizeArrayFiles(&$file_post) {
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
    
        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
    
        return $file_ary;
    }
}
?>