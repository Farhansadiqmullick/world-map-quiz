<?php

/**
 * Quiz Content of The Plugin
 *
 * @package Quiz Class
 */
namespace WMQ\src;

use WMQ\src\templates\map_content;
use WMQ\src\Helpers;

/**
 * Quiz Class Component
 */
class QUIZ {
	/**
	 * Answer Columns

	 * @var string $answer_column
	 */
	public static $answer_column = 5;
	/**
	 * All the Answers of the Column

	 * @var string $answers
	 */
	public static $answers = [
		'afghanistan',
		'albania',
		'algeria',
		'andorra',
		'angola',
		'antiguaandbarbuda',
		'argentina',
		'armenia',
		'australia',
		'austria',
		'azerbaijan',
		'bahamas',
		'bahrain',
		'bangladesh',
		'barbados',
		'belarus',
		'belgium',
		'belize',
		'benin',
		'bhutan',
		'bolivia',
		'bosniaandherzegovina',
		'botswana',
		'brazil',
		'brunei',
		'bulgaria',
		'burkinafaso',
		'burma',
		'burundi',
		'cambodia',
		'cameroon',
		'canada',
		'capeverde',
		'centralafricanrepublic',
		'chad',
		'chile',
		'china',
		'colombia',
		'comoros',
		'costarica',
		'cotedivoire',
		'croatia',
		'cuba',
		'cyprus',
		'czechrepublic',
		'democraticrepublicofthecongo',
		'denmark',
		'djibouti',
		'dominica',
		'dominicanrepublic',
		'easttimor',
		'ecuador',
		'egypt',
		'elsalvador',
		'equatorialguinea',
		'eritrea',
		'estonia',
		'ethiopia',
		'federatedstatesofmicronesia',
		'fiji',
		'finland',
		'france',
		'gabon',
		'gambia',
		'georgia',
		'germany',
		'ghana',
		'greece',
		'grenada',
		'guatemala',
		'guinea',
		'guineabissau',
		'guyana',
		'haiti',
		'honduras',
		'hungary',
		'iceland',
		'india',
		'indonesia',
		'iran',
		'iraq',
		'ireland',
		'israel',
		'italy',
		'jamaica',
		'japan',
		'jordan',
		'kazakhstan',
		'kenya',
		'kiribati',
		'kosovo',
		'kuwait',
		'kyrgyzstan',
		'laos',
		'latvia',
		'lebanon',
		'lesotho',
		'liberia',
		'libya',
		'liechtenstein',
		'lithuania',
		'luxembourg',
		'macedonia',
		'madagascar',
		'malawi',
		'malaysia',
		'maldives',
		'mali',
		'malta',
		'marshallislands',
		'mauritania',
		'mauritius',
		'mexico',
		'moldova',
		'monaco',
		'mongolia',
		'montenegro',
		'morocco',
		'mozambique',
		'namibia',
		'nauru',
		'nepal',
		'netherlands',
		'newzealand',
		'nicaragua',
		'niger',
		'nigeria',
		'northkorea',
		'norway',
		'oman',
		'pakistan',
		'palau',
		'panama',
		'papuanewguinea',
		'paraguay',
		'peru',
		'philippines',
		'poland',
		'portugal',
		'qatar',
		'republicofthecongo',
		'romania',
		'russia',
		'rwanda',
		'saintkittsandnevis',
		'saintlucia',
		'saintvincentandthegrenadines',
		'samoa',
		'sanmarino',
		'saotomeandprincipe',
		'saudiarabia',
		'senegal',
		'serbia',
		'seychelles',
		'sierraleone',
		'singapore',
		'slovakia',
		'slovenia',
		'solomonislands',
		'somalia',
		'southafrica',
		'southkorea',
		'southsudan',
		'spain',
		'srilanka',
		'sudan',
		'suriname',
		'swaziland',
		'sweden',
		'switzerland',
		'syria',
		'taiwan',
		'tajikistan',
		'tanzania',
		'thailand',
		'togo',
		'tonga',
		'trinidadandtobago',
		'tunisia',
		'turkey',
		'turkmenistan',
		'tuvalu',
		'uganda',
		'ukraine',
		'unitedarabemirates',
		'unitedkingdom',
		'unitedstates',
		'uruguay',
		'uzbekistan',
		'vanuatu',
		'vaticancity',
		'venezuela',
		'vietnam',
		'yemen',
		'zambia',
		'zimbabwe',
	];
	/**
	 * Initialize the hook of Quiz
	 */
	function quiz_init() {
		get_header();
		$map_content   = new Map_Content();
		$option_values = get_option('wmq_get_values');
		if ( $option_values ) {
			echo '<div class="world-map-quiz-container">';
			printf('%s %s %s %s', esc_attr($map_content->get_heading_content()), esc_attr($map_content->get_body_content()), esc_attr($map_content->table_content()), esc_attr($this->name_details()));
			echo '</div>';
		}
		get_footer();
	}

	/**
	 * Name Details of the Quiz Table
	 */
	function name_details() {
		$helpers = new Helpers();
		$content = <<<HEREDOC
        <form name="form1" id="calcform" onSubmit="return false;">
        <div class="container" style="margin-top: 10px">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-sm country-table">
                        {$helpers->getValues(self::$answer_column)}
                        {$helpers->country_names(self::$answers)}
                    </table>
                </div>
            </div>
        </div>
        <input type="hidden" id="numberguesses" name="numberguesses" value="196" /><br /><br />
    </form>
HEREDOC;

		return $content;
	}
}
