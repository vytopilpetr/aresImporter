<?php

namespace App\Controller;

use App\Form\CompanySearchType;
use App\Service\CompanyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController {

    #[Route('/importCompany', name: 'company_import')]
    public function companyImportAction(Request $request, CompanyService $companyService): Response
    {
        $success = null;
        $form = $this->createForm(CompanySearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $companyId = $data['companyId'];

            $success = $companyService->importCompany($companyId);
        }

        return $this->render('company/import.html.twig', [
            'form' => $form->createView(),
            'success' => $success,
        ]);
    }

    #[Route('/rest/api/{companyId}', requirements: ['companyId' => '\d+'], methods: ['GET'])]
    public function provideCompanyAction(int $companyId, CompanyService $companyService): JsonResponse
    {
        $company = $companyService->getCompany($companyId);
        if (!empty($company)) {
            return new JsonResponse($company);
        }

        return new JsonResponse(['error' => 'No data found'], 404);
    }

}
