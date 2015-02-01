<?php
namespace Sotnik\GridBundle\Extend;

interface ConfirmableInterface
{
    /**
     * @param string $confirmText
     * @param string $confirmButtonText
     * @return mixed
     */
    public function setConfirm($confirmText, $confirmButtonText);

    /**
     * @return bool
     */
    public function getIsConfirmed();

    /**
     * @return string
     */
    public function getConfirmText();

    /**
     * @return string
     */
    public function getConfirmButtonText();
}
