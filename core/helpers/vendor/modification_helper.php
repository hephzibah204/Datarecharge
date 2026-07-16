<?php

class ModificationHelper {
    
    public function calculateModificationFee($modificationType, $siteSettings = null) {
        // Default base fees
        $baseFees = [
            'name' => 5000,
            'phone' => 5000,
            'address' => 4000,
            'email' => 4000,
            'dob' => 28574,
            'lga' => 3000,
            'gender' => 8000,
            'marital_status' => 6000,
            'affidavit' => 5000,
            'birth_certificate' => 10000,
        ];
        
        // Get site admin configurable fees
        $adminFees = [];
        if ($siteSettings) {
            $adminFees = [
                'fee_name_mod' => (float)$siteSettings->feeNameMod ?? $baseFees['name'],
                'fee_phone_mod' => (float)$siteSettings->feePhoneMod ?? $baseFees['phone'],
                'fee_address_mod' => (float)$siteSettings->feeAddressMod ?? $baseFees['address'],
                'fee_email_mod' => (float)$siteSettings->feeEmailMod ?? $baseFees['email'],
                'fee_dob_mod' => (float)$siteSettings->feeDobMod ?? $baseFees['dob'],
                'fee_lga_mod' => (float)$siteSettings->feeLgaMod ?? $baseFees['lga'],
                'fee_gender_mod' => (float)$siteSettings->feeGenderMod ?? $baseFees['gender'],
                'fee_marital_mod' => (float)$siteSettings->feeMaritalMod ?? $baseFees['marital_status'],
                'fee_affidavit' => (float)$siteSettings->feeAffidavit ?? $baseFees['affidavit'],
                'fee_birth_certificate' => (float)$siteSettings->feeBirthCertificate ?? $baseFees['birth_certificate'],
            ];
        }
        
        return $adminFees;
    }
    
    public function validateModificationRequirements($modificationType, $userData, $documents = []) {
        $requirements = [
            'name' => [
                'min_documents' => 2,
                'required_docs' => ['court_affidavit', 'newspaper_publication'],
                'max_length' => 100,
                'needs_verification' => true,
            ],
            'phone' => [
                'min_documents' => 1,
                'required_docs' => ['police_report'],
                'max_length' => 20,
                'needs_verification' => true,
            ],
            'address' => [
                'min_documents' => 2,
                'required_docs' => ['utility_bill', 'rental_agreement'],
                'max_length' => 200,
                'needs_verification' => true,
            ],
            'email' => [
                'min_documents' => 1,
                'required_docs' => ['id_card'],
                'max_length' => 100,
                'needs_verification' => true,
            ],
            'dob' => [
                'min_documents' => 2,
                'required_docs' => ['birth_certificate', 'attestation'],
                'max_length' => 30,
                'needs_verification' => true,
            ],
            'lga' => [
                'min_documents' => 2,
                'required_docs' => ['utility_bill', 'community_letter'],
                'max_length' => 100,
                'needs_verification' => true,
            ],
            'gender' => [
                'min_documents' => 1,
                'required_docs' => ['court_affidavit'],
                'max_length' => 20,
                'needs_verification' => true,
            ],
            'marital_status' => [
                'min_documents' => 1,
                'required_docs' => ['marriage_certificate_or_divorce'],
                'max_length' => 50,
                'needs_verification' => true,
            ],
            'affidavit' => [
                'min_documents' => 1,
                'required_docs' => ['court_affidavit'],
                'max_length' => 200,
                'needs_verification' => false,
            ],
            'birth_certificate' => [
                'min_documents' => 1,
                'required_docs' => ['birth_certificate', 'attestation'],
                'max_length' => 200,
                'needs_verification' => false,
            ],
        ];
        
        $errors = [];
        $req = $requirements[$modificationType] ?? null;
        
        if (!$req) {
            $errors[] = "Unsupported modification type: $modificationType";
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Validate document count
        if (count($documents) < $req['min_documents']) {
            $errors[] = "At least {$req['min_documents']} documents required for {$modificationType} modification";
        }
        
        // Check required document types
        foreach ($req['required_docs'] as $required_doc) {
            $found = false;
            foreach ($documents as $doc) {
                if ($doc['type'] === $required_doc) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $errors[] = "Required document type: $required_doc missing";
            }
        }
        
        // Validate data format
        if (isset($userData['new_value'])) {
            $value = $userData['new_value'];
            if (strlen($value) > $req['max_length']) {
                $errors[] = "Value for {$modificationType} exceeds maximum length of {$req['max_length']}";
            }
        }
        
        return ['valid' => count($errors) === 0, 'errors' => $errors];
    }
    
    public function calculateProcessingTime($modificationType, $urgency = 'normal') {
        $baseTimes = [
            'name' => ['normal' => 5, 'urgent' => 2, 'express' => 1],
            'phone' => ['normal' => 3, 'urgent' => 1, 'express' => 0.5],
            'address' => ['normal' => 4, 'urgent' => 2, 'express' => 1],
            'email' => ['normal' => 3, 'urgent' => 1, 'express' => 0.5],
            'dob' => ['normal' => 7, 'urgent' => 4, 'express' => 2],
            'lga' => ['normal' => 4, 'urgent' => 2, 'express' => 1],
            'gender' => ['normal' => 6, 'urgent' => 3, 'express' => 2],
            'marital_status' => ['normal' => 5, 'urgent' => 3, 'express' => 2],
            'affidavit' => ['normal' => 3, 'urgent' => 1, 'express' => 0.5],
            'birth_certificate' => ['normal' => 5, 'urgent' => 2, 'express' => 1],
        ];
        
        $days = $baseTimes[$modificationType][$urgency] ?? $baseTimes['name'][$urgency];
        return ['days' => $days, 'hours' => $days * 24];
    }
    
    public function getModificationMetadata($modificationType) {
        $metadata = [
            'name' => [
                'category' => 'demographic',
                'status' => 'restricted',
                'requires_court' => true,
                'requires_publication' => true,
            ],
            'phone' => [
                'category' => 'contact',
                'status' => 'standard',
                'requires_police' => true,
            ],
            'address' => [
                'category' => 'address',
                'status' => 'standard',
                'requires_utility' => true,
            ],
            'email' => [
                'category' => 'contact',
                'status' => 'standard',
                'requires_id' => true,
            ],
            'dob' => [
                'category' => 'demographic',
                'status' => 'restricted',
                'requires_birth_cert' => true,
                'requires_attestation' => true,
            ],
            'lga' => [
                'category' => 'address',
                'status' => 'standard',
                'requires_community' => true,
            ],
            'gender' => [
                'category' => 'demographic',
                'status' => 'restricted',
                'requires_court' => true,
            ],
            'marital_status' => [
                'category' => 'demographic',
                'status' => 'standard',
                'requires_marriage_cert' => true,
            ],
            'affidavit' => [
                'category' => 'document',
                'status' => 'standard',
                'requires_court' => true,
            ],
            'birth_certificate' => [
                'category' => 'document',
                'status' => 'standard',
                'requires_birth_cert' => true,
            ],
        ];
        
        return $metadata[$modificationType] ?? null;
    }
}

