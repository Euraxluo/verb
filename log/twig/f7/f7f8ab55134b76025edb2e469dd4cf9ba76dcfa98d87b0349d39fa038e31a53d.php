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

/* layouts/layout.html */
class __TwigTemplate_0f9ff47c4d8d49bf491077776d081e1686ce8e560122a148d1f3b1a57a55cf81 extends \Twig\Template
{
    private $source;

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<!DOCTYPE html>

<body>
<header>header</header>
<content>
";
        // line 6
        $this->displayBlock('content', $context, $blocks);
        // line 8
        echo "</content>
<footer>footer</footer>
</body>

</html>";
    }

    // line 6
    public function block_content($context, array $blocks = [])
    {
    }

    public function getTemplateName()
    {
        return "layouts/layout.html";
    }

    public function getDebugInfo()
    {
        return array (  53 => 6,  45 => 8,  43 => 6,  36 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>

<body>
<header>header</header>
<content>
{% block content %}
{% endblock %}
</content>
<footer>footer</footer>
</body>

</html>", "layouts/layout.html", "/mnt/c/home/php/euraxluo/app/views/layouts/layout.html");
    }
}
