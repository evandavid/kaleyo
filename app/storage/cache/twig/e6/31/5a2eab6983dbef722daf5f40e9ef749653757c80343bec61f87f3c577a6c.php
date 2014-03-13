<?php

/* base/components/flash.twig */
class __TwigTemplate_e6315a2eab6983dbef722daf5f40e9ef749653757c80343bec61f87f3c577a6c extends Twig_Template
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
        // line 1
        $context["__internal_92721e4eb2de2d7daafbf5d60a71830ff8e4f2295bfb49572d90bdbf59038548"] = $this->env->loadTemplate("macros/bootstrap.twig");
        // line 2
        if (isset($context["flash"])) { $_flash_ = $context["flash"]; } else { $_flash_ = null; }
        if ($this->getAttribute($_flash_, "success")) {
            // line 3
            echo "\t";
            if (isset($context["flash"])) { $_flash_ = $context["flash"]; } else { $_flash_ = null; }
            echo $context["__internal_92721e4eb2de2d7daafbf5d60a71830ff8e4f2295bfb49572d90bdbf59038548"]->getalert($this->getAttribute($_flash_, "success"), "success");
            echo "
";
        }
        // line 5
        if (isset($context["flash"])) { $_flash_ = $context["flash"]; } else { $_flash_ = null; }
        if ($this->getAttribute($_flash_, "error")) {
            // line 6
            echo "\t";
            if (isset($context["flash"])) { $_flash_ = $context["flash"]; } else { $_flash_ = null; }
            echo $context["__internal_92721e4eb2de2d7daafbf5d60a71830ff8e4f2295bfb49572d90bdbf59038548"]->getalert($this->getAttribute($_flash_, "error"), "error");
            echo "
";
        }
        // line 8
        if (isset($context["flash"])) { $_flash_ = $context["flash"]; } else { $_flash_ = null; }
        if ($this->getAttribute($_flash_, "warning")) {
            // line 9
            echo "\t";
            if (isset($context["flash"])) { $_flash_ = $context["flash"]; } else { $_flash_ = null; }
            echo $context["__internal_92721e4eb2de2d7daafbf5d60a71830ff8e4f2295bfb49572d90bdbf59038548"]->getalert($this->getAttribute($_flash_, "warning"), "warning");
            echo "
";
        }
        // line 11
        if (isset($context["flash"])) { $_flash_ = $context["flash"]; } else { $_flash_ = null; }
        if ($this->getAttribute($_flash_, "info")) {
            // line 12
            echo "\t";
            if (isset($context["flash"])) { $_flash_ = $context["flash"]; } else { $_flash_ = null; }
            echo $context["__internal_92721e4eb2de2d7daafbf5d60a71830ff8e4f2295bfb49572d90bdbf59038548"]->getalert($this->getAttribute($_flash_, "info"), "info");
            echo "
";
        }
    }

    public function getTemplateName()
    {
        return "base/components/flash.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  51 => 11,  41 => 8,  24 => 3,  19 => 1,  83 => 17,  68 => 15,  53 => 8,  38 => 6,  34 => 6,  29 => 3,  26 => 2,  20 => 1,  257 => 127,  254 => 126,  211 => 71,  208 => 70,  200 => 62,  197 => 61,  193 => 52,  186 => 50,  183 => 49,  174 => 45,  171 => 44,  155 => 15,  152 => 14,  145 => 129,  142 => 126,  126 => 111,  123 => 70,  120 => 69,  117 => 68,  114 => 67,  107 => 61,  98 => 53,  96 => 44,  76 => 26,  74 => 14,  71 => 13,  61 => 14,  54 => 12,  21 => 2,  14 => 12,  110 => 65,  100 => 46,  92 => 45,  87 => 44,  84 => 43,  79 => 42,  62 => 29,  44 => 9,  31 => 5,  28 => 125,);
    }
}
