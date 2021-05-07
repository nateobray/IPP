<?php

namespace obray\ipp\enums;

class MultipleDocumentHandling extends \obray\ipp\types\Enum
{
    const single_document = "single-document";
    const separate_documents_uncollated_copies = "separate-documents-uncollated-copies";
    const separate_documents_collated_copies = "separate-documents-collated-copies";
    const single_document_new_sheet = "single-document-new-sheet";
}