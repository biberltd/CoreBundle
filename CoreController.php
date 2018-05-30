<?php
/**
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        15.01.2015
 */

namespace BiberLtd\Bundle\CoreBundle;

use BiberLtd\Bundle\CoreBundle\CoreTraits\DebugTrait;
use BiberLtd\Bundle\CoreBundle\CoreTraits\LocalizationTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller {
	/**
	 * @var null|BiberLtd\Bundle\AccessManagementBundle\Services\AccessValidator
	 */
	public $av;
	/**
	 * @var array
	 */
	public $body;
	/**
	 * @var array
	 */
	public $flash;
	/**
	 * @var array
	 */
	public $head;        // Holds details of head: css, js
	/**
	 * @var bool
	 */
	public $initialized = false;
	/**
	 * @var null|BiberLtd\Bundle\MultilanguageSupport\Entity\Language
	 */
	public $language;    /** Current locale's language entity. */
	/**
	 * @var string
	 */
	public $locale;      /** Current locale */
	/**
	 * @var array
	 */
	public $page;        /** Array that contains current page details */
	/**
	 * @var string
	 */
	public $previousUrl; /** stores previous url */
	/**
	 * @var object
	 */
	public $renderResponse; /** Render response */
	/**
	 * @var null|BiberLtd\Bundle\SiteManagementBundle\Entity\Site
	 */
	public $site;        /** Current site */
	/**
	 * @var null|BiberLtd\Bundle\AccessManagementBundle\Services\SessionManager
	 */
	public $sm;          /** Session manager */
	/**
	 * @var \DateTimeZone
	 */
	public $timezone;    /** new \DateTimeZone identifies app time zone */
	/**
	 * @var object
	 */
	public $translator;  /** Translator */
	/**
	 * @var array
	 */
	public $url;         /** Array Base urls.  */
	/** @var array */
	public $vars;
	/** @var    object      $session */
	protected $session = null;
	/** @var    array       Holds admin arrays. Set with setAdminPs() method.  */
	private $adminIps = [];
	/**
	 * @var object
	 */
	protected $model;
	/**
	 * @var string
	 */
	public $theme = '';
	/**
	 * @var Request
	 */
	public $httpRequest = null;
	use DebugTrait,
		LocalizationTrait;

	/**
	 * @param int|null    $siteId
	 * @param string|null $pageCode
	 * @param string|null $theme
	 */
	public function init(int $siteId = null, string $pageCode = null, string $theme = null, Request $request = null){
		$this->model = new \stdClass();
		$this->httpRequest = $request;
		$this->initCore($theme, $request);

		$this->model->mls = $this->get('multilanguagesupport.model');
		$this->model->site = $this->get('sitemanagement.model');
		$response = $this->model->mls ->getLanguage($this->locale);
		$this->language = false;
		if(!$response->error->exist){
			$this->language = $response->result->set;
		}
		unset($response);
		/** Get current site */
		if(is_null($siteId)){
			$siteId = $this->session->get('_currentSiteId');
		}
		$response = $this->model->site->getSite($siteId);

		$this->site = false;
		if(!$response->error->exist){
			$this->site = $response->result->set;
		}
		unset($response);

		if(!is_null($pageCode)){
			$this->model->cms = $this->get('cms.model');
			$response = $this->model->cms->getPage($pageCode);

			$this->page['entity'] = false;

			if(!$response->error->exist) {
				$this->page['entity'] = $response->result->set;
				$this->theme = $this->page['entity']->getLayout()->getTheme()->getFolder();
			}
			/** Get page blocks */
			$response = $this->model->cms->listModulesOfPageLayoutsGroupedBySection($this->page['entity'], array('sort_order' => 'asc'));
			if ($response->error->exist) {
				/** Show error if no modules can be loaded */
				echo $this->translator->trans('msg.error.modules', [], 'systemMessages');
				exit;
			}
			$this->page['blocks'] = $response->result->set;
			unset($response);
		}
		if (!is_null($theme)) {
			$this->theme = $theme;
		}else{
			if (!isset($this->theme) || is_null($this->theme)) {
				$this->theme = $this->get('kernel')->getContainer()->getParameter('current_theme');
			}
		}
		$this->vars['xssCode'] = $this->generateXssCode();
		if(!is_null($pageCode) && $request instanceof  Request && strpos($request->getPathInfo(), '|-') === false){
			$this->get('session')->set('previousUrl', $request->getPathInfo());
		}
		$this->initialized = true;
	}

	/**
	 * @param string|null                                    $theme
	 * @param \Symfony\Component\HttpFoundation\Request|null $request
	 */
	public function initCore(string $theme = null, Request $request = null){
		$this->previousUrl = $this->get('session')->get('previousUrl');
		$this->setURLs($theme, null, $request);
		$this->body = array(
			'analytics' => '',
			'js'        => [],
			'classes'   => [],
		);
		$this->head = array(
			'css'   => [],
			'js'    => [],
		);

		$this->timezone = new \DateTimeZone($this->container->getParameter('app_timezone'));

		$this->session = $this->session ?? $this->get('session');

		$this->locale =  $this->session->get('_locale');
		$this->translator = $this->get('translator');
		$this->translator->setLocale($this->locale);
		$this->av = $this->get('access_validator');
		$this->sm = $this->get('session_manager');
	}

	/**
	 * @param array|null  $css
	 * @param array|null  $js
	 * @param array|null  $vars
	 * @param string|null $theme
	 *
	 * @return array
	 */
	public function initDefaults(array $css = null, array $js = null, array $vars = null, string $theme = null, Request $request = null) {
		$theme = $theme ?? $this->get('kernel')->getContainer()->getParameter('current_theme');
		$dCss = [];
		$dJs= [];
		if (is_null($css)) {
			$css = $dCss;
		} else {
			$css = array_merge_recursive($dCss, $css);
		}

		if (is_null($js)) {
			$js = $dJs;
		} else {
			$js = array_merge_recursive($dJs, $js);
		}
		$preConfCss = '';
		$preConfJs = '';
		if (!isset($this->url) || is_null($this->url)) {
			$this->setURLs($theme, null, $request);
		}
		$defaultLinks = $this->url;
		foreach ($css as $item) {
			$preConfCss .= '<link rel="stylesheet" href="' . $this->url['themes'] . '/' . $theme . '/' . $item . '" />' . PHP_EOL;
		}
		foreach ($js as $item) {
			$preConfJs .= '<script type="text/javascript" src="' . $this->url['themes'] . '/' . $theme . '/' . $item . '"></script>' . PHP_EOL;
		}
		/** *** */
		$defaults = array(
			'doctype' => '<!DOCTYPE html>',
			'conditional_classes' => '',
			'css' => $preConfCss,
			'js' => $preConfJs,
			'head'  => array(
				'css'   => $preConfCss,
				'js'    => $preConfJs,
			),
			'locale' => $this->container->get('request')->getLocale(),
			'link' => $this->url,
		);

		if (!is_null($vars)) {
			if (isset($vars['link'])) {
				$links = array_merge($defaultLinks, $vars['link']);
				$defaults['link'] = $links;
				unset($vars['link'], $links);
			}
			$defaults = array_merge($defaults, $vars);
		}
		return $defaults;
	}

	/**
	 * @param string $prefix
	 *
	 * @return string
	 *
	 * UID's are timestamp based codes. Since id's cannot start with a number in HTML, it requires a prefix. The
	 * id then glued with the current timestamps last 5 digits. This is done so that the generated id won't be too
	 * long.
	 */
	public function generateUid(string $prefix = null){
		$prefix = $prefix ?? 'uid-';
		return $prefix.substr(microtime(true), -5);
	}

	/**
	 * @return string
	 *
	 * Cross Site Scripting Prevention Token Generator
	 */
	public function generateXssCode(){
		if(!isset($this->session)){
			$this->session = $this->container->get('session');
		}
		$currentXssCode = $this->session->get('_xss');
		$currentXssTime = $this->session->get('_xss_timestamp');

		$ip = $this->httpRequest === null ? '127.0.0.1' : $this->httpRequest->getClientIp();
		$now = microtime(true);
		if (!$currentXssTime || !$currentXssCode) {
			$currentXssCode = md5($ip . $now);
			$this->session->set('_xss', $currentXssCode);
			$this->session->set('_xss_timestamp', $now);
			return $currentXssCode;
		}

		$timeDifference = $now - $currentXssTime;
		/**
		 * 120 seconds * 5 = 600 seconds = 10 minutes
		 */
		if ($timeDifference > 600) {
			/** If time difference since last request is more than 10 minutes change security code alongside with timestamp. */
			$now = microtime(true);
			$this->session->set('_xss', md5($ip . $now));
			$this->session->set('_xss_timestamp', $now);
		}
		else{
			/** If the last request happenned earliear than 10 minutes just update timestamp and increase life span of hash code. */
			$this->session->set('_xss_timestamp', $now);
		}
		return $currentXssCode;
	}

	/**
	 * @param string      $action
	 * @param bool|null   $redirect
	 * @param string|null $type
	 * @param string|null $msg
	 * @param string|null        $route
	 * @param array       $params
	 * @param int|null    $status
	 * @param bool        $https
	 *
	 * @return bool|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function ifAccessNotGranted(string $action, bool $redirect = null, string $type = null, string $msg = null, string $route = null, array $params = [], int $status = null, bool $https){
		$redirect = $redirect ?? false;
		$type = $type ?? 'danger';
		$msg = $msg ?? '';
		$status = $status ?? 302;
		$https = $https ?? false;
		if(!$this->initialized){
			$this->initCore();
		}
		if(!$this->av->isActionGranted($action)){
			if($redirect){
				return $this->redirectWithMessage($type, $msg, $route, $params, $status, $https, null);
			}
		}
		return false;
	}

	/**
	 * @param bool|null   $redirect
	 * @param string|null $route
	 * @param bool|null   $https
	 *
	 * @return bool|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function ifLoggedin(bool $redirect = null, string $route = null, bool $https = null){
		$redirect = $redirect ?? false;
		$https = $https ?? false;

		if(!$this->initialized){
			$this->initCore();
		}
		$accessMap = array(
			'unmanaged' => false,
			'guest' => false,
			'authenticated' => true,
			'members' => [],
			'groups' => [],
			'status' => array('a')
		);
		if ($this->av->hasAccess(null, $accessMap)) {
			if(!$redirect){
				return true;
			}
			return $this->redirectAdvanced($route, [], 302, $https);
		}
		return false;
	}

	/**
	 * @param array       $group
	 * @param bool|null   $redirect
	 * @param string|null $route
	 * @param bool|null   $https
	 *
	 * @return bool|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function ifMemberOfGroups(array $group, bool $redirect = null, string $route = null, bool $https = null){
		$redirect = $redirect ?? false;
		$https = $https ?? false;
		if(!$this->initialized){
			$this->initCore();
		}
		$accessMap = array(
			'unmanaged' => false,
			'guest' => false,
			'authenticated' => true,
			'members' => [],
			'groups' => $group,
			'status' => ['a']
		);
		if ($this->av->hasAccess(null, $accessMap)) {
			if(!$redirect){
				return true;
			}
			return $this->redirectAdvanced($route, [], 302, $https);
		}
		return false;
	}

	/**
	 * @param bool        $redirect
	 * @param string|null $route
	 * @param bool        $https
	 *
	 * @return bool|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function ifNotMember(bool $redirect = null, string $route = null, bool $https = null){
		$https = $https ?? null;
		$redirect = $redirect ?? null;
		if(!$this->initialized){
			$this->initCore();
		}
		/**
		 * @todo bind it to database
		 */
		$accessMap = array(
			'unmanaged' => false,
			'guest' => false,
			'authenticated' => true,
			'members' => [],
			'groups' => [],
			'status' => array('a')
		);
		if (!$this->av->hasAccess(null, $accessMap)) {
			if(!$redirect){
				return true;
			}
			return $this->redirectAdvanced($route, [], 302, $https);
		}
		return false;
	}

	/**
	 * @param        $actionCode
	 * @param string $route
	 *
	 * @return bool
	 */
	public function ifRevokedAction(string $actionCode, string $route = null){
		$route = $route ?? '';
		if(!$this->initialized){
			$this->initCore();
		}
		if ($this->av->isActionRevoked($actionCode)) {
			$this->sm->logAction($actionCode, 1, array('route' => $route, 'accessGranted' => false));
			return true;
		}
		return false;
	}

	/**
	 * @param string $xssCode
	 *
	 * @return bool
	 */
	public function isValidXss(string $xssCode){
		$savedXssCode = $this->session->get('_xss');
		if($savedXssCode === $xssCode){
			return true;
		}
		return false;
	}

	/**
	 * @param           $data
	 * @param bool|null $assoc
	 *
	 * @return mixed
	 */
	public function jsonDecode($data, bool $assoc = null){
		$assoc = $assoc ?? false;
		return json_decode(stripslashes($data),$assoc);
	}

	/**
	 * @param string $data
	 *
	 * @return mixed
	 */
	public function jsonEncode(string $data){
		return preg_replace_callback(
			'/\\\\u([0-9a-zA-Z]{4})/',
			function ($matches) {
				return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');
			},
			json_encode($data)
		);
	}

	/**
	 * @param $response
	 *
	 * @deprecated Will be deleted in Q2 of 2016.
	 */
	public function mergeResponseBodyAndHead(array $response){
		$body = null;
		$head = null;
		if(isset($response['body'])){
			$body = $response['body'];
		}
		if(isset($response['head'])){
			$head = $response['head'];
		}
		if(!is_null($body)){
			if(isset($body['analytics'])){
				$this->body['analytics'][] = $body['analytics'];
			}
			if(isset($body['classes'])){
				foreach($body['classes'] as $item){
					$this->body['classes'][] = $item;
				}
			}
			if(isset($body['js'])){
				foreach($body['js'] as $item){
					$this->body['js'][] = $item;
				}
			}
		}
		if(!is_null($head)){
			if(isset($head['css'])){
				foreach($head['css'] as $item){
					$this->head['css'][] = $item;
				}
			}
			if(isset($head['js'])){
				foreach($head['js'] as $item){
					$this->head['js'][] = $item;
				}
			}
		}
	}

	/**
	 * @param bool|null $appendLocale
	 * @param bool|null $prependBoot
	 * @param bool|null $parameters
	 * @param bool|null $forceHttps
	 *
	 * @return string
	 */
	public function prepareUrl(bool $appendLocale = null, bool $prependBoot = null, bool $parameters = null, bool $forceHttps = null, Request $request = null) {
		$appendLocale = $appendLocale ?? true;
		$prependBoot = $prependBoot ?? false;
		$forceHttps = $forceHttps ?? false;
		$host = $request instanceof Request ? $request->getHttpHost() : '';
		$protocol = 'http://';
		if (($request instanceof Request && $request->isSecure()) || $forceHttps) {
			$protocol = 'https://';
		}
		$url = $protocol . $host;
		/**
		 * Make sure to add app_dev if the environment is set to dev
		 */
		if ($prependBoot) {
			if ($this->get('kernel')->getEnvironment() == 'dev') {
				$url .= '/app_dev.php';
			} else {
				$url .= '/app.php';
			}
		}
		if ($appendLocale) {
			$url .= '/' . (!is_null($request) ? $request->getLocale() : '');
		}
		if (!is_null($parameters)) {
			if (is_array($parameters)) {
				$url = $url . implode('/', $parameters);
			} else {
				$url .= $parameters;
			}
		}
		return $url;
	}

	/**
	 * @param array $css
	 *
	 * @return string
	 */
	public function prepareCss(array $css = []) {
		$css = array_unique($css);
		$cssStr = '';
		foreach ($css as $item) {
			if(strpos($item, 'http') !== false){
				$cssStr .= '<script type="text/javascript" src="'.$item.'"></script>'.PHP_EOL;
			}
			else{
				$cssStr .= '<link rel="stylesheet" href="' . $this->url['domain'].'/themes/'.$this->theme.$item.'" />'.PHP_EOL;
			}
		}
		return $cssStr;
	}

	/**
	 * @param obecjt|null $session
	 *
	 * @return array
	 */
	public function prepareFlash($session = null) {
		$flash = array(
			'exist' => false,
			'type' => null,
			'message' => null,
			'optional'=> null
		);
		if(is_null($session)){
			$session = $this->session;
		}
		$statuses = $session->getFlashBag()->get('msg.status');
		$types = $session->getFlashBag()->get('msg.type');
		$contents = $session->getFlashBag()->get('msg.content');
		$optional = $session->getFlashBag()->get('optional');
		$optional = count($optional) > 0 ? $optional[0] : '';
		if (isset($statuses[0])) {
			if ($statuses[0]) {
				$flash = array(
					'exist' => true,
					'type' => $types[0],
					'message' => $contents[0],
					'optional' => $optional,
				);
			}
		}
		return $flash;
	}

	/**
	 * @param array $js
	 *
	 * @return string
	 */
	public function prepareJs(array $js = []) {
		$js = array_unique($js);
		$jsStr = '';
		foreach ($js as $item) {
			if (strpos($item, 'http') !== false) {
				$jsStr .= '<script type="text/javascript" src="' . $item . '"></script>' . PHP_EOL;
			}
			else {
				$jsStr .= '<script type="text/javascript" src="' . $this->url['domain'] . '/themes/' . $this->theme . $item . '"></script>' . PHP_EOL;
			}
		}

		return $jsStr;
	}

	/**
	 * @param int      $page
	 * @param int      $total
	 * @param int      $limit
	 * @param int|null $maxPage
	 *
	 * @return array
	 */
    public function preparePagination(int $page, int $total, int $limit, int $maxPage = null) {
        $maxPage = $maxPage ?? 10;
        /**
         * Calculate number of pages.
         */
        $numberOfPages = 0;
        if ($total % $limit > 0) {
            $numberOfPages = ceil($total / $limit);
        } else {
            $numberOfPages = ceil($total / $limit);
        }
        $numberOfPages = $numberOfPages < 1 ? 1 : $numberOfPages;

        /**
         * Build pagination
         */
        $pagination = [];

        $pagination['currentPage'] = $page;
        $pagination['numberOfPages'] = $numberOfPages;
        $pagination['visiblePages'] = $maxPage;
        $middle = ceil($maxPage / 2);
        /**
         * Decide where to place the dots
         */
        if ($page > $middle) {
            $pagination['firstFarWay'] = true;
        } else {
            $pagination['firstFarWay'] = false;
        }
        if ($page <= ($numberOfPages - $middle)) {
            $pagination['lastFarAway'] = true;
        } else {
            $pagination['lastFarAway'] = false;
        }

        if ($pagination['firstFarWay'] && $pagination['lastFarAway']) {
            $items = [];
            $items[] = $page;
            $count = 1;
            $reverseCounter = $middle - 1;
            $forwardCounter = $middle;
            for($reverseCounter; $reverseCounter > 0; $reverseCounter--){
                array_unshift($items, ($page - $count));
                $count++;
            }
            $count = 1;
            for($forwardCounter; $forwardCounter < $maxPage; $forwardCounter++){
                $items[] = ($page + $count);
                $count++;
            }
            $pagination['items'] = $items;
        } else if ($pagination['firstFarWay'] && !$pagination['lastFarAway']) {
            $startWith = 1;
            if($numberOfPages > $maxPage){
                $startWith = $numberOfPages > $maxPage;
            }
            for ($i = $startWith; $i <= $numberOfPages; $i++) {
                $pagination['items'][] = $i;
            }
        } else if (!$pagination['firstFarWay'] && $pagination['lastFarAway']) {
            $endWith = $maxPage;
            if($numberOfPages < $maxPage){
                $endWith = $numberOfPages;
            }
            for ($i = 1; $i <= $endWith; $i++) {
                $pagination['items'][] = $i;
            }
        }
        else{
            for($i = 1; $i <= $numberOfPages; $i++){
                $pagination['items'][] = $i;
            }
        }
        $pagination['prev'] = $page-1 <=0 ? 1 : $page-1;
        $pagination['next'] = $page + 1 > $numberOfPages ? $numberOfPages : $page + 1;

        return $pagination;
    }

	/**
	 * @param string    $routeName
	 * @param array     $parameters
	 * @param int|null  $status
	 * @param bool|null $https
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function redirectAdvanced(string $routeName, array $parameters = [], int $status = null, bool $https = null) {
		$status = $status ?? 200;
		$https = $https ?? false;
		$url = $this->generateUrl($routeName, $parameters);
		if($https){
			$url = str_replace('http://', 'https://', $url);
		}
		return new RedirectResponse($url, $status);
	}

	/**
	 * @param string      $msgType
	 * @param string      $msgContent
	 * @param string|null $to
	 * @param array       $params
	 * @param int|null    $status
	 * @param bool|null   $https
	 * @param null        $optional
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function redirectWithMessage(string $msgType, string $msgContent, string $to = null, array $params = [], int $status = null, bool $https = null, $optional = null) {
		$to = $to ?? '404';
		$status = $status ?? 302;
		$https = $https ?? false;
		$response = $this->redirectAdvanced($to, $params, $status, $https);
		$this->session->getFlashBag()->add('msg.status', true);
		$this->session->getFlashBag()->add('msg.type', $msgType);
		/** $response[$code] must have a corresponding translation */
		$this->session->getFlashBag()->add('msg.content', $msgContent);
		$this->session->getFlashBag()->add('optional', $optional);
		return $response;
	}

	/**
	 * @return mixed
	 */
	public function renderPage(){
		$this->flash = $this->prepareFlash($this->session);
		if(!isset($this->vars['flash'])){
			$this->vars['flash'] = $this->flash;
		}
		if(!isset($this->vars['page'])){
			$this->vars['page'] = array(
				'blocks'        => $this->page['blocks'],
				'entity'        => $this->page['entity'],
			);
			if(!isset($this->vars['page']['meta'])){
				$this->vars['page']['meta'] = array(
					'description'   => $this->page['entity']->getLocalization($this->locale)->getMetaDescription(),
					'keywords'      => $this->page['entity']->getLocalization($this->locale)->getMetaKeywords(),
					'title'         => $this->page['entity']->getLocalization($this->locale)->getTitle(),
				);
			}
		}
		else{
			if(!isset($this->vars['page']['blocks'])){
				$this->vars['page']['blocks'] = $this->page['blocks'];
			}
			if(!isset($this->vars['page']['entity'])){
				$this->vars['page']['entity'] = $this->page['entity'];
			}
			if(!isset($this->vars['page']['meta'])){
				$this->vars['page']['meta'] = array(
					'description'   => $this->page['entity']->getLocalization($this->locale)->getMetaDescription(),
					'keywords'      => $this->page['entity']->getLocalization($this->locale)->getMetaKeywords(),
					'title'         => $this->page['entity']->getLocalization($this->locale)->getTitle(),
				);
			}
		}
		if(!isset($this->vars['site'])){
			$this->vars['site']['entity'] = $this->site;
			$this->vars['site']['name'] = $this->site->getTitle();
		}
		if(!isset($this->vars['link'])){
			$this->vars['link'] = $this->url;
		}
		if(!isset($this->vars['locale'])){
			$this->vars['locale'] = $this->locale;
		}

		$this->vars['body'] = $this->body;
		$this->vars['head'] = $this->head;
		if(!isset($this->head['css'])){
			$this->head['css'] = '/css/styles.min.css';
		}
		$this->vars['body']['classes'] = array_unique($this->vars['body']['classes']);
		$this->vars['head']['css'] = array_merge($this->head['css'], $this->vars['head']['css']);
		$this->vars['body']['js'] = $this->prepareJs($this->vars['body']['js']);
		$this->vars['head']['js'] = $this->prepareJs($this->vars['head']['js']);
		$this->vars['head']['css'] = $this->prepareCss($this->vars['head']['css']);
		/** *** */

		/**
		 * Container
		 */
		$pagePath = $this->page['entity']->getBundleName().':'.$this->page['entity']->getLayout()->getTheme()->getFolder().'/Pages:'.$this->page['entity']->getCode().'.html';

		/**
		 * @var Container $container
		 */
		switch($this->container->getParameter('templating.engines')[0]){
			case 'twig':
				$pagePath .= '.twig';
				break;
			case 'smarty':
				$pagePath .= '.smarty';
				break;

		}
		/** *** */
		return $this->render($pagePath, $this->vars);
	}

	/**
	 * @return $this
	 */
	public function resetRenderResponse() {
		$this->response['renderCode'] = '';
		$this->response['body']['js'] = [];
		$this->response['head']['css'] = [];
		$this->response['head']['js'] = [];
		$this->response['html'] = '';
		return $this;
	}

	/**
	 * @param array $ips
	 *
	 * @return object
	 */
	public function setAdminIps(array $ips){
		$this->adminIps = $ips;

		return $this->session;
	}

	/**
	 * @param string      $content
	 * @param string|null $type
	 * @param bool|null   $status
	 *
	 * @return object
	 */
	public function setFlashMessage(string $content, string $type = null, bool $status = null){
		$type = $type ?? 'danger';
		$status = $status ?? true;
		$this->session->getFlashBag()->add('msg.status', $status);
		$this->session->getFlashBag()->add('msg.type', $type);
		$this->session->getFlashBag()->add('msg.content', $content);

		return $this->session;
	}

	/**
	 * @param string      $theme
	 * @param string|null $backend
	 *
	 * @return $this
	 */
	public function setURLs(string $theme = null, string $backend = null, Request $request = null) {
		$theme = $theme ?? '';
		$backend = $backend ?? 'manage';
		$forceHttps = true;
		if ($request instanceof Request && $request->isSecure()) {
            $forceHttps = true;
		}
		if ($this->get('kernel')->getEnvironment() == 'dev') {
			$url['base_l'] = $this->prepareUrl(true, true, null, $forceHttps, $request);
			$url['https_l'] = $this->prepareUrl(true, true, null,true, $request);
		} else {
			$url['base_l'] = $this->prepareUrl(true, false, null, $forceHttps, $request);
			$url['https_l'] = $this->prepareUrl(true, false, null,true, $request);
		}
		if ($this->get('kernel')->getEnvironment() == 'dev') {
			$url['base'] = $this->prepareUrl(false, true, null, $forceHttps, $request);
			$url['https'] = $this->prepareUrl(false, true, null,true, $request);
		} else {
			$url['base'] = $this->prepareUrl(false, false, null, $forceHttps, $request);
			$url['https'] = $this->prepareUrl(false, false, null, true, $request);
		}
		unset($force_https);

		$url['domain'] = str_replace('/app_dev.php', '', $url['base']);
		$url['themes'] = $url['domain'].'/themes';
		$url['current_theme'] = $url['themes'].'/'.$theme;
		$url['cdn'] = $url['domain'].'/cdn';
		$url['icons'] = $url['current_theme'].'/img/icons';
		$url['manage'] = $url['base_l'].'/'.$backend;
		$this->url = $url;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function persistFlashMessages(){
		foreach ($this->session->getFlashBag()->all() as $key => $flash) {
			$this->session->getFlashBag()->set($key,$flash);
		}
		return true;
	}
}