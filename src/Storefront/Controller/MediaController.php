<?php declare(strict_types=1);

namespace Tinect\MediaStaging\Storefront\Controller;

use League\Flysystem\FilesystemInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class MediaController extends StorefrontController
{
    private FilesystemInterface $filesystem;
    private SystemConfigService $systemConfigService;

    public function __construct(
        FilesystemInterface $filesystem,
        SystemConfigService $systemConfigService
    ) {
        $this->filesystem = $filesystem;
        $this->systemConfigService = $systemConfigService;
    }


    #[Route(path:"/mediaproxy/{wildcard}", name:"frontend.mediaproxy.index", options:["seo" => "false"], methods:["GET"], defaults:["XmlHttpRequest" => false], requirements:["wildcard" => ".*"])]
    public function mediaproxy(string $wildcard, Request $request, SalesChannelContext $context): RedirectResponse
    {
        $queryString = $request->getQueryString();
        $url = explode('/mediaproxy', $request->getUri())[0];

        if (!$this->filesystem->has($wildcard)) {
            $url = rtrim($this->systemConfigService->getString('TinectMediaStaging.config.liveurl'), '/');
        }

        return new RedirectResponse($this->getUrlWithQueryString($url . '/' . $wildcard, $queryString));
    }

    private function getUrlWithQueryString(string $url, ?string $queryString): string
    {
        if (!$queryString) {
            return $url;
        }

        return $url . '?' . $queryString;
    }
}
