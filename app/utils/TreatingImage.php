<?php

namespace app\utils;

class TreatingImage extends UploadFiles
{
    /**
     * Imagem (GD)
     * @var 
     */
    private $image;

    /**
     * Tipo de Imagem
     * @var string
     */
    private $type;

    /* Método responsável por carregar os dados da imagem */
    public function __construct($file)
    {
        //INFORMAÇÕES DA IMAGEM ORIGINAL
        $this->image = imagecreatefromstring(file_get_contents($file));
        $info = pathinfo($file);
        $this->type = $info['extension'] == 'jpg' || $info['extension'] == 'JPG' ? 'jpeg' : $info['extension'];
    }

    /** Método responsável por redimensionar a imagem 
     * @param integer $new_width
     * @param integer $new_heigth
     * @return
     * 
     * y - altura
     * x - largura
     * y * x
     */
    public function resize($new_width, $new_height = -1)
    {
        $img_width = imagesx($this->image);
        $img_height = imagesy($this->image);

        if ($new_width > $img_width) {

            $this->image;
            return true;

        } else {

            $this->image = imagescale($this->image, $new_width, $new_height);
            return true;
        }
    }

    /**
     * Método responsável por salvar a imagem no caminho definido
     * @param string $localFile
     * @param integer $quality (0 - 100)
     */
    public function save($localFile, $quality = 100)
    {
        $this->output($localFile, $quality);
    }

    /**
     * Método responsável por imprimir a imagem na tela
     * @param integer $quality (0 - 100)
     */
    public function print($quality = 100)
    {
        header('Content-Type: image/' . $this->type);
        $this->output(null, $quality);
        exit;
    }

    /** 
     * Método responsável por executar a saída da imagem
     * @param string $localFile
     * @param integer $quality
     */
    private function output($localFile, $quality = 100)
    {

        switch ($this->type) {

            case 'jpeg':
                imagejpeg($this->image, $localFile, $quality);
                break;

            case 'png':
                $quality = 9;
                imagesavealpha($this->image, true);
                imagepng($this->image, $localFile, $quality);
                break;

            case 'webp':
                imagewebp($this->image, $localFile, $quality);
                break;

            case 'bmp':
                imagebmp($this->image, $localFile, $quality);
                break;

            case 'gif':
                imagegif($this->image, $localFile, $quality);
                break;
        }
    }
}
