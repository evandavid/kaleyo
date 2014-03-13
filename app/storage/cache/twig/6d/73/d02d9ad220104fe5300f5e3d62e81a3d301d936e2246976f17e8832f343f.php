<?php

/* demo.html.twig */
class __TwigTemplate_6d73d02d9ad220104fe5300f5e3d62e81a3d301d936e2246976f17e8832f343f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("base/fluid.html.twig");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base/fluid.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "            <header>
                <a href=\"https://github.com/vanting/RedSlim\"><h1>Welcome to RedSlim!</h1></a>
                <p>
                Congratulations! Your RedSlim application is running. 
                This framework is composed of a few popular components. If this is
                your first time using RedSlim, start with the introduction of these components in the left menu.
                </p>
            </header>
            
            <section style=\"padding-top: 20px\">
                <h3>Say HI to me~</h3>
                <form method=\"post\" action=\"";
        // line 15
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_app_, "urlFor", array(0 => "guest_comment"), "method"), "html", null, true);
        echo "\">
                  <div class=\"input-prepend\">
                    <span class=\"add-on\">@</span>
                    <input class=\"input-medium\" name=\"name\" id=\"name\" type=\"text\" placeholder=\"Anonymous\">
                  </div>
                  <div class=\"input-append\">
                    <input class=\"input-xxlarge\" name=\"message\" id=\"message\" type=\"text\" placeholder=\"What's on your mind?\">
                    <button class=\"btn\" type=\"submit\">Post</button>
                  </div>        
                </form>          
            </section>
            
            <section style=\"padding-bottom: 20px\">
                <p>
                    <a href=\"";
        // line 29
        if (isset($context["app"])) { $_app_ = $context["app"]; } else { $_app_ = null; }
        echo twig_escape_filter($this->env, $this->getAttribute($_app_, "urlFor", array(0 => "api_comment_json"), "method"), "html", null, true);
        echo "\"> >> Get these messages in json format</a>
                </p>
                <div class=\"row-fluid\">
                    <div class=\"span8\">
                        <table class=\"table table-striped table-condensed table-hover\">
                            <thead>
                             <tr>
                                 <th>Date</th>           
                                 <th>Who</th>           
                                 <th>Message</th> 
                             </tr>
                         </thead>
                             <tbody>
                             ";
        // line 42
        if (isset($context["guests"])) { $_guests_ = $context["guests"]; } else { $_guests_ = null; }
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable($_guests_);
        foreach ($context['_seq'] as $context["_key"] => $context["guest"]) {
            // line 43
            echo "                                 <tr>
                                     <td>";
            // line 44
            if (isset($context["guest"])) { $_guest_ = $context["guest"]; } else { $_guest_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_guest_, "modify_date"), "html", null, true);
            echo "</td>    
                                     <td>";
            // line 45
            if (isset($context["guest"])) { $_guest_ = $context["guest"]; } else { $_guest_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_guest_, "name"), "html", null, true);
            echo " @";
            if (isset($context["guest"])) { $_guest_ = $context["guest"]; } else { $_guest_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_guest_, "ip"), "html", null, true);
            echo "</td>     
                                     <td>";
            // line 46
            if (isset($context["guest"])) { $_guest_ = $context["guest"]; } else { $_guest_ = null; }
            echo twig_escape_filter($this->env, $this->getAttribute($_guest_, "message"), "html", null, true);
            echo "</td>
                                 </tr>
                             ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['guest'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 49
        echo "                             </tbody>
                         </table>  
                    </div>
                </div>
            </section>
";
    }

    public function getTemplateName()
    {
        return "demo.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  110 => 49,  100 => 46,  92 => 45,  87 => 44,  84 => 43,  79 => 42,  62 => 29,  44 => 15,  31 => 4,  28 => 3,);
    }
}
