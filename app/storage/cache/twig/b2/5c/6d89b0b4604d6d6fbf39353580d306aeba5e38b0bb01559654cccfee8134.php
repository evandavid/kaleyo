<?php

/* macros/bootstrap.twig */
class __TwigTemplate_b25c6d89b0b4604d6d6fbf39353580d306aeba5e38b0bb01559654cccfee8134 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 12
        echo "
";
        // line 19
        echo "
";
    }

    // line 2
    public function getalert($_message = null, $_type = null, $_style = null)
    {
        $context = $this->env->mergeGlobals(array(
            "message" => $_message,
            "type" => $_type,
            "style" => $_style,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 3
            echo "\t";
            if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
            if (($_message_ != "")) {
                // line 4
                echo "\t\t<div class=\"alert alert-";
                if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
                echo twig_escape_filter($this->env, ((array_key_exists("type", $context)) ? (_twig_default_filter($_type_, "success")) : ("success")), "html", null, true);
                echo " alert-block\">
\t\t\t<a class=\"close\" data-dismiss=\"alert\">×</a>
\t\t\t<span style=\"";
                // line 6
                if (isset($context["style"])) { $_style_ = $context["style"]; } else { $_style_ = null; }
                echo twig_escape_filter($this->env, ((array_key_exists("style", $context)) ? (_twig_default_filter($_style_, "font-weight: bold;")) : ("font-weight: bold;")), "html", null, true);
                echo "\">
\t\t\t";
                // line 7
                if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                echo twig_escape_filter($this->env, $_message_, "html", null, true);
                echo "
\t\t\t</span>
\t\t</div>
\t";
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 13
    public function getlabel($_message = null, $_type = null)
    {
        $context = $this->env->mergeGlobals(array(
            "message" => $_message,
            "type" => $_type,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 14
            echo "\t";
            if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
            if (($_message_ == "")) {
                // line 15
                echo "\t";
            } else {
                // line 16
                echo "\t<span class=\"label label-";
                if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
                echo twig_escape_filter($this->env, $_type_, "html", null, true);
                echo "\">";
                if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
                echo twig_escape_filter($this->env, $_message_, "html", null, true);
                echo "</span>
\t";
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 20
    public function getbtn($_message = null, $_type = null, $_attributes = null, $_element = null, $_size = null)
    {
        $context = $this->env->mergeGlobals(array(
            "message" => $_message,
            "type" => $_type,
            "attributes" => $_attributes,
            "element" => $_element,
            "size" => $_size,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 21
            echo "\t<";
            if (isset($context["element"])) { $_element_ = $context["element"]; } else { $_element_ = null; }
            echo twig_escape_filter($this->env, $_element_, "html", null, true);
            echo " class=\"btn btn-";
            if (isset($context["size"])) { $_size_ = $context["size"]; } else { $_size_ = null; }
            echo twig_escape_filter($this->env, $_size_, "html", null, true);
            echo " btn-";
            if (isset($context["type"])) { $_type_ = $context["type"]; } else { $_type_ = null; }
            echo twig_escape_filter($this->env, $_type_, "html", null, true);
            echo "\" ";
            if (isset($context["attributes"])) { $_attributes_ = $context["attributes"]; } else { $_attributes_ = null; }
            echo twig_escape_filter($this->env, ((array_key_exists("attributes", $context)) ? (_twig_default_filter($_attributes_, "")) : ("")), "html", null, true);
            echo ">";
            if (isset($context["message"])) { $_message_ = $context["message"]; } else { $_message_ = null; }
            echo twig_escape_filter($this->env, $_message_, "html", null, true);
            echo "</";
            if (isset($context["element"])) { $_element_ = $context["element"]; } else { $_element_ = null; }
            echo twig_escape_filter($this->env, $_element_, "html", null, true);
            echo ">
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "macros/bootstrap.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  111 => 20,  89 => 15,  85 => 14,  73 => 13,  56 => 7,  40 => 3,  27 => 2,  22 => 19,  51 => 6,  41 => 8,  24 => 3,  19 => 12,  83 => 17,  68 => 15,  53 => 8,  38 => 6,  34 => 6,  29 => 3,  26 => 2,  20 => 1,  257 => 127,  254 => 126,  211 => 71,  208 => 70,  200 => 62,  197 => 61,  193 => 52,  186 => 50,  183 => 49,  174 => 45,  171 => 44,  155 => 15,  152 => 14,  145 => 129,  142 => 126,  126 => 21,  123 => 70,  120 => 69,  117 => 68,  114 => 67,  107 => 61,  98 => 53,  96 => 44,  76 => 26,  74 => 14,  71 => 13,  61 => 14,  54 => 12,  21 => 2,  14 => 12,  110 => 65,  100 => 46,  92 => 16,  87 => 44,  84 => 43,  79 => 42,  62 => 29,  44 => 4,  31 => 5,  28 => 125,);
    }
}
