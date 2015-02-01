<?php
namespace Sotnik\GridBundle\Extend;

class Confirmable implements ConfirmableInterface
{

    private $isConfirmed = false;

    private $confirmText = 'Do you really want to do this?';

    private $confirmButtonText = 'OK';


    /**
     * @param string $confirmText
     * @param string $confirmButtonText
     * @return $this;
     */
    public function setConfirm($confirmText = null, $confirmButtonText = null)
    {
        $this->isConfirmed = true;

        if ($confirmText) {
            $this->confirmText = $confirmText;
        }

        if ($confirmButtonText) {
            $this->confirmButtonText = $confirmButtonText;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

    /**
     * @return string
     */
    public function getConfirmText()
    {
        return $this->confirmText;
    }

    /**
     * @return string
     */
    public function getConfirmButtonText()
    {
        return $this->confirmButtonText;
    }
}
