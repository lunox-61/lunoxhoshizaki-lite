<?php

namespace LunoxHoshizaki\Mail;

use LunoxHoshizaki\View\View;

abstract class Mailable
{
    public string $subject = 'Notification';
    protected string $viewName = '';
    protected array $viewData = [];

    /**
     * Build the message.
     */
    abstract public function build(): self;

    /**
     * Set the subject.
     */
    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set the view.
     */
    public function view(string $view, array $data = []): self
    {
        $this->viewName = $view;
        $this->viewData = $data;
        return $this;
    }

    /**
     * Render the mailable to a string.
     */
    public function render(): string
    {
        if (empty($this->viewName)) {
            return '';
        }
        
        return View::make($this->viewName, $this->viewData);
    }
}
