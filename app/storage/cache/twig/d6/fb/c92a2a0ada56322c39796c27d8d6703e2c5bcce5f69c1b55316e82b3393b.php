<?php

/* base/sidebar.twig */
class __TwigTemplate_d6fbc92a2a0ada56322c39796c27d8d6703e2c5bcce5f69c1b55316e82b3393b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'sidebar' => array($this, 'block_sidebar'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $this->displayBlock('sidebar', $context, $blocks);
    }

    public function block_sidebar($context, array $blocks = array())
    {
        // line 2
        echo "\t<div class=\"well sidebar-nav\">
            <ul class=\"nav nav-list\">
            \t<li class=\"nav-header\">Documentation</li>
\t          ";
        // line 5
        if (isset($context["pmenu"])) { $_pmenu_ = $context["pmenu"]; } else { $_pmenu_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_pmenu_);
        foreach ($context['_seq'] as $context["_key"] => $context["link"]) {
            echo " 
\t          \t<li ";
            // line 6
            if (isset($context["link"])) { $_link_ = $context["link"]; } else { $_link_ = null; }
            echo (($this->getAttribute($_link_, "active")) ? ("class=\"active\"") : (""));
            echo "><a href=\"";
            if (isset($context["link"])) { $_link_ = $context["link"]; } else { $_link_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_link_, "url"), "html", null, true);
            echo "\">";
            if (isset($context["link"])) { $_link_ = $context["link"]; } else { $_link_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_link_, "desc"), "html", null, true);
            echo "</a></li>
\t          ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['link'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 8
        echo "            </ul>
        </div><!--/.well -->
        
        <div class=\"well sidebar-nav\">
            <ul class=\"nav nav-list\">
            \t<li class=\"nav-header\">Installation</li>
\t          ";
        // line 14
        if (isset($context["smenu"])) { $_smenu_ = $context["smenu"]; } else { $_smenu_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_smenu_);
        foreach ($context['_seq'] as $context["_key"] => $context["link"]) {
            echo " 
\t          \t<li ";
            // line 15
            if (isset($context["link"])) { $_link_ = $context["link"]; } else { $_link_ = null; }
            echo (($this->getAttribute($_link_, "active")) ? ("class=\"active\"") : (""));
            echo "><a href=\"";
            if (isset($context["link"])) { $_link_ = $context["link"]; } else { $_link_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_link_, "url"), "html", null, true);
            echo "\">";
            if (isset($context["link"])) { $_link_ = $context["link"]; } else { $_link_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_link_, "desc"), "html", null, true);
            echo "</a></li>
\t          ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['link'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 17
        echo "            </ul>
        </div><!--/.well -->
";
    }

    public function getTemplateName()
    {
        return "base/sidebar.twig";
    }

    public function getDebugInfo()
    {
        return array (  83 => 17,  68 => 15,  53 => 8,  38 => 6,  34 => 4,  29 => 3,  26 => 2,  20 => 1,  257 => 127,  254 => 126,  211 => 71,  208 => 70,  200 => 62,  197 => 61,  193 => 52,  186 => 50,  183 => 49,  174 => 45,  171 => 44,  155 => 15,  152 => 14,  145 => 129,  142 => 126,  126 => 111,  123 => 70,  120 => 69,  117 => 68,  114 => 67,  107 => 61,  98 => 53,  96 => 44,  76 => 26,  74 => 14,  71 => 13,  61 => 14,  54 => 1,  21 => 60,  14 => 12,  110 => 65,  100 => 46,  92 => 45,  87 => 44,  84 => 43,  79 => 42,  62 => 29,  44 => 15,  31 => 5,  28 => 125,);
    }
}
