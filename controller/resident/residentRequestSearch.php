<?php
require_once '../../model/resident/residentCertificateModel.php';

// Initialize the certificate request model
$residentCertificateRequestModel = new CertificateRequestModel(); // Assuming this is the correct model for certificate requests

// Check if a search query is set
if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $residentID = isset($_POST['resident_id']) ? $_POST['resident_id'] : (isset($_SESSION['resident_id']) ? $_SESSION['resident_id'] : null); // Use resident_id from POST, fallback to session, or null if not set

    // Fetch certificate requests based on the search query
    $requests = $residentCertificateRequestModel->searchRequests($conn, $residentID, $query);

    if (!empty($requests)) {
        foreach ($requests as $request) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($request['request_id']) . "</td>";
            echo "<td>" . htmlspecialchars($request['document_name']) . "</td>";
            echo "<td>" . htmlspecialchars($request['name_requested']) . "</td>";
            echo "<td>" . htmlspecialchars($request['purpose']) . "</td>";
            echo "<td>" . htmlspecialchars($request['date_submitted']) . "</td>";
            echo "<td>" . htmlspecialchars($request['last_updated']) . "</td>";
            echo "<td>" . (isset($request['payment_type']) && !empty($request['payment_type']) ? htmlspecialchars($request['payment_type']) : 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($request['payment_amount']) . "</td>";
            echo "<td>";
            if (!empty($request['proof_of_payment'])) {
                $proofOfPaymentPath = '../../img/proof_of_payment/' . htmlspecialchars($request['proof_of_payment']);
                echo "<a href='" . $proofOfPaymentPath . "' target='_blank'>";
                echo "<img src='" . $proofOfPaymentPath . "' alt='Proof of Payment' width='50' height='50' style='margin-right:5px;'>";
                echo "</a>";
            } else {
                echo "No proof of payment";
            }
            echo "</td>";
            echo "<td>" . htmlspecialchars($request['no_of_copies']) . "</td>";
            echo "<td>" . (!empty($request['remarks']) ? (strlen($request['remarks']) > 16 ? htmlspecialchars(substr($request['remarks'], 0, 16)) . "..." : htmlspecialchars($request['remarks'])) : "N/A") . "</td>";
            $statusText = ($request['status'] === 'approved') ? 'available' : htmlspecialchars($request['status']);
            $statusColor = ($request['status'] === 'approved') ? 'green' : (($request['status'] === 'pending') ? 'orange' : (($request['status'] === 'rejected' || $request['status'] === 'disapproved') ? 'red' : (($request['status'] === 'issued') ? 'gray' : 'gray')));
            echo "<td><span style='
                background-color: $statusColor; 
                color: white; 
                padding: 5px 10px; 
                border-radius: 5px; 
                font-weight: bold; 
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); 
                text-shadow: -1px -1px 0 black, 
                             1px -1px 0 black, 
                             -1px 1px 0 black, 
                             1px 1px 0 black;'>"
                . htmlspecialchars($statusText) . "</span></td>";
            echo "<td>";
            // View Button
            if ($request['status'] === 'rejected' || $request['status'] === 'issued') {
                echo "<button class='modal-open-view button' aria-haspopup='true' style='background-color:rgb(92, 92, 92);'
                    data-request-id='" . $request['request_id'] . "'
                    data-document-type='" . htmlspecialchars($request['document_name']) . "'
                    data-purpose='" . htmlspecialchars($request['purpose']) . "'
                    data-date-submitted='" . htmlspecialchars($request['date_submitted']) . "'
                    data-payment-type-id='" . htmlspecialchars($request['payment_type_id']) . "'
                    data-proof-of-payment='" . htmlspecialchars($request['proof_of_payment']) . "'
                    data-payment-amount='" . htmlspecialchars($request['payment_amount']) . "'
                    data-no-of-copies='" . htmlspecialchars($request['no_of_copies']) . "'
                    data-status='" . htmlspecialchars($request['status']) . "'
                    data-remarks='" . htmlspecialchars($request['remarks']) . "'>
                    <i class='fa-solid fa-eye'></i>
                </button>";
            }
            // Edit Button
            if ($request['status'] === 'approved' || $request['status'] === 'pending') {
                echo "<button class='modal-open button' aria-haspopup='true'
                    data-request-id='" . $request['request_id'] . "'
                    data-document-type='" . htmlspecialchars($request['document_name']) . "'
                    data-purpose='" . htmlspecialchars($request['purpose']) . "'
                    data-date-submitted='" . htmlspecialchars($request['date_submitted']) . "'
                    data-payment-type-id='" . htmlspecialchars($request['payment_type_id']) . "'
                    data-proof-of-payment='" . htmlspecialchars($request['proof_of_payment']) . "'
                    data-payment-amount='" . htmlspecialchars($request['payment_amount']) . "'
                    data-no-of-copies='" . htmlspecialchars($request['no_of_copies']) . "'
                    data-status='" . htmlspecialchars($request['status']) . "'
                    data-remarks='" . htmlspecialchars($request['remarks']) . "'
                    onclick='populateModal(this)'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pen' viewBox='0 0 16 16'>
                        <path d='m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z'/>
                    </svg>
                </button>";
            }
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='13'>No requests found.</td></tr>";
    }
}
?>