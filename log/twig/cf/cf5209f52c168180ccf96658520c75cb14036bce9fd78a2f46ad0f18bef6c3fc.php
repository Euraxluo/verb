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

/* index.html */
class __TwigTemplate_60f19451787ccc99a9038b32c334377bbe292d896852a3938f918ceb158b9684 extends \Twig\Template
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
        $this->parent = $this->loadTemplate("layouts/layout.html", "index.html", 1);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = [])
    {
        // line 3
        echo twig_escape_filter($this->env, ($context["data"] ?? null), "html", null, true);
        echo "
<H1> <?php echo \$title;?></H1>
<H3> <?php echo \$data;?> </H3>
";
    }

    public function getTemplateName()
    {
        return "index.html";
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
<H1> <?php echo \$title;?></H1>
<H3> <?php echo \$data;?> </H3>
{% endblock %}

", "index.html", "/mnt/c/home/php/euraxluo/app/views/index.html");
    }
}
