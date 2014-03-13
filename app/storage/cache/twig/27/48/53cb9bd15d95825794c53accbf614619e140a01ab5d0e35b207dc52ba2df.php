<?php

/* base/stylesheets.twig */
class __TwigTemplate_274853cb9bd15d95825794c53accbf614619e140a01ab5d0e35b207dc52ba2df extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $this->displayBlock('stylesheets', $context, $blocks);
    }

    public function block_stylesheets($context, array $blocks = array())
    {
        // line 2
        echo "
\t<link rel=\"stylesheet\" href=\"";
        // line 3
        if (isset($context["assetUri"])) { $_assetUri_ = $context["assetUri"]; } else { $_assetUri_ = null; }
        echo twig_escape_filter($this->env, $_assetUri_, "html", null, true);
        echo "/css/bootstrap.min.css\">
        <link rel=\"stylesheet\" href=\"";
        // line 4
        if (isset($context["assetUri"])) { $_assetUri_ = $context["assetUri"]; } else { $_assetUri_ = null; }
        echo twig_escape_filter($this->env, $_assetUri_, "html", null, true);
        echo "/css/bootstrap-responsive.min.css\">

";
    }

    public function getTemplateName()
    {
        return "base/stylesheets.twig";
    }

    public function getDebugInfo()
    {
        return array (  34 => 4,  29 => 3,  26 => 2,  20 => 1,  257 => 127,  254 => 126,  211 => 71,  208 => 70,  200 => 62,  197 => 61,  193 => 52,  186 => 50,  183 => 49,  174 => 45,  171 => 44,  155 => 15,  152 => 14,  145 => 129,  142 => 126,  126 => 111,  123 => 70,  120 => 69,  117 => 68,  114 => 67,  107 => 61,  98 => 53,  96 => 44,  76 => 26,  74 => 14,  71 => 13,  61 => 6,  54 => 1,  21 => 60,  14 => 12,  110 => 65,  100 => 46,  92 => 45,  87 => 44,  84 => 43,  79 => 42,  62 => 29,  44 => 15,  31 => 4,  28 => 125,);
    }
}
