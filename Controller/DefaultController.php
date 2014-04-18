<?php

namespace Prismic\Bundle\PrismicBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
        $ctx = $this->get('prismic.context');
        $docs = $ctx->getApi()->forms()->everything->ref ($ctx->getRef ())->submit();

        return array(
            'ctx' => $ctx,
            'docs' => $docs
        );
    }

    /**
     * @Route("/documents/{id}/{slug}", name="detail")
     * @Template()
     */
    public function detailAction($id, $slug)
    {
        $ctx = $this->get('prismic.context');
        $doc = $ctx->getDocument($id);

        if ($doc) {

            if ($doc->getSlug() == $slug) {
                return array(
                    'ctx' => $ctx,
                    'doc' => $doc
                );
            }

            if (in_array($slug, $doc->getSlugs())) {
                return $this->redirect(
                    $this->generateUrl('detail', array('id' => $id, 'slug' => $doc->slug(), 'ref' => $ctx->getMaybeRef()))
                );
            }

        } 

        throw $this->createNotFoundException('Document not found');
    }

    /**
     * @Route("/search", name="search")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        $q = $request->query->get('q');
        $ctx = $this->get('prismic.context');
        $docs = $ctx->getApi()->forms()->everything->ref ($ctx->getRef ())->query(
            '[[:d = fulltext(document, "'.$q.'")]]'
        )->submit();

        return array(
            'ctx' => $ctx,
            'docs' => $docs
        );
    }

}
