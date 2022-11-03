<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileBrowserController extends BaseController
{
    /**
     * The controller that opens elFinder when the user presses the folde icon under "Min Vektor".
     *
     * @return Response
     */
    public function browse(): Response
    {
        return $this->forward('FMElfinderBundle:ElFinder:show', array(
            'instance' => 'admin_access',
        ));
    }

    /**
     * Cam be used to stream a binary file to requesting user.
     * If a user requests a file that is on a path that routes to this controller, the file will be streamed
     * to user.
     *
     * @param Request $request
     *
     * @return BinaryFileResponse
     */
    public function fileStream(Request $request): BinaryFileResponse
    {
        $prefix = substr($request->getPathInfo(), 1); //removes leading '/'
        //Had some trouble with paths. Differenet behaviours on different systems...
        $prefix = str_replace('%20', ' ', $prefix); //Must replace the %20 that blank space is replaced with in the request
        $prefix = str_replace('%5C', '%2F', $prefix); //Must replace the %5C that / is replaced with in the request (in some browsers only?)
        return new BinaryFileResponse($prefix);
    }

}
