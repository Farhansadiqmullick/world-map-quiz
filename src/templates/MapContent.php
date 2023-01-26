<?php

/**
 * Template Name: World Map Template
 * Template Post Type: page, post
 */

namespace WMQ\src\templates;


class MapContent
{
     protected $getOptionvalues;

    public function __construct()
    {
        $this->getOptionvalues = get_option('wmq_get_values');
    }

    function getHeadingContent()
    {
        $content = <<<HEREDOC
<nav class="navbar navbar-light justify-content-between" style="background: {$this->getOptionvalues['nav_background_color']}">
<a class="navbar-brand" href="/" style="color: white; font-weight: 700">
    <img src="" style="height: 28px; margin-right: 5px" />
    {$this->getOptionvalues['header_nav_title']} <span style="color: {$this->getOptionvalues['header_span_color']}">{$this->getOptionvalues['header_span_title']}</span>
</a>
</nav>
HEREDOC;

        return $content;
    }

    function getBodyContent()
    {
        $content = <<<HEREDOC
<div class="container" style="margin-bottom: 10px; margin-top: 20px">
<h3 style="text-align: center">{$this->getOptionvalues['heading']}</h3>
<div style="text-align: center">{$this->getOptionvalues['subheading']}</div>
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <nav class="navbar" data-spy="affix" data-offset-top="197" style="z-index: 10">
            <form class="form-inline">
                <div class="form-group">
                    <div class="input-group" id="answerbar">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input class="text-primary" onClick="stopInputTime()" id="notimer" type="button" value="{$this->getOptionvalues['wmq_timer_text']}" />
                            </div>
                            <div class="input-group-text">
                                <div style="color: red" id="timer"></div>
                            </div>
                        </div>
                        <span id="starttyping">
                            <input class="form-control" onkeyup="calc1()" data-correct="{$this->getOptionvalues['wmq_correct_answer']}" data-allanswer="{$this->getOptionvalues['wmq_all_answer']}" placeholder="type answers here" autofocus type="text" autocomplete="off" id="guess" name="guess" />
                        </span>
                        <div class="input-group-append">
                            <input onClick="showallanswers()" class="btn btn-danger" id="showanswersbutton" type="button" value="{$this->getOptionvalues['wmq_give_up_title']}" style="margin-left: 10px" />
                            <input style="display: none" onClick="reloadedpage()" id="reloadpage" type="button" class="btn btn-primary" value="{$this->getOptionvalues['wmq_try_again_title']}" style="margin-left: 10px" />
                        </div>
                    </div>
                </div>
            </form>
        </nav>
    </div>
</div>
</div>
<div id="score" class="bg-light"></div>


<input type="hidden" id="quiztime" value="{$this->getOptionvalues['quiz_time']}" />
<input type="hidden" id="worldmapquiz" name="worldmapquiz" value="yes" />
<br />
HEREDOC;

        return $content;
    }

    function tableContent()
    {

        $content = <<<HEREDOC
<div class="table-responsive">
<table class="text-center mx-auto">
    <tr>
        <td>
            <div id="mapWrapper" data-backgroundColor="{$this->getOptionvalues['world_bg_color']}" data-country={$this->getOptionvalues['country_color']} data-hover="{$this->getOptionvalues['hover_country_color']}" data-score="{$this->getOptionvalues['score_country_color']}" style="
            text-align: center;
            position: relative;
            width: {$this->getOptionvalues['map_width']}px;
            height: {$this->getOptionvalues['map_height']}px;
          ">
                <div id="points" style="z-index: 2; position: relative">
                    <!-- <div id="ANDORRA"class="p"style="top:30px;left:120px;">&nbsp</div> -->
                </div>
                <div id="map" style="width: {$this->getOptionvalues['map_width']}px; height: {$this->getOptionvalues['map_height']}px"></div>
            </div>
        </td>
    </tr>
</table>
</div>
<br />
HEREDOC;

        return $content;
    }
}
