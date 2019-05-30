<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* login.html */
class __TwigTemplate_63fb1f11bc0f785e71ef07826bc66bf04829e7a8641c8544d25e3fcede13a395 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 1
        return "layouts/layout.html";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $this->parent = $this->loadTemplate("layouts/layout.html", "login.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = [])
    {
        // line 3
        echo twig_escape_filter($this->env, ($context["data"] ?? null), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "login.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  47 => 3,  44 => 2,  34 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% extends \"layouts/layout.html\" %}
{% block content %}
{{ data }}
{% endblock %}

", "login.html", "/mnt/c/home/php/euraxluo/app/views/login.html");
    }
}
