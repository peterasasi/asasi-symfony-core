<?php
namespace Dbh\SfCoreBundle\ArgumentResolver;


use by\component\paging\vo\PagingParams;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * 分页参数 可以自动获取page_index,page_size
 * Class PagingParamValueResolver
 * @package App\ArgumentResolver
 */
class PagingParamValueResolver implements ArgumentValueResolverInterface
{

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return PagingParams::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $pagingParams = new PagingParams();
        $pageIndex = $request->get('page_index', 1);
        $pagingParams->setPageIndex(intval($pageIndex) - 1);

        $pageSize = $request->get('page_size', 10);
        $pagingParams->setPageSize(intval($pageSize));

        // 限制 每页大小
        if ($pagingParams->getPageSize() > 500) {
            $pagingParams->setPageSize(500);
        }

        yield $pagingParams;
    }

}
