<?php
require_once '../../model/admin/adminCertificateModel.php';

// Initialize the permit model
$adminCertificateRequestModel = new CertificateRequestModel(); // Assuming this is the correct model for certificate requests

// Check if a search query is set
if (isset($_POST['query'])) {
    $query = $_POST['query'];

    // Fetch permit requests based on the search query
    $requests = $adminCertificateRequestModel->searchClearanceRequests($conn, $query);

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
            echo "<td>" . htmlspecialchars($request['template_price']) . "</td>";
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
            echo "</td>";
            echo "<td>" . htmlspecialchars($request['no_of_copies']) . "</td>";
            $statusColor = ($request['status'] === 'approved') ? 'green' : 
                           (($request['status'] === 'pending') ? 'orange' : 
                           (($request['status'] === 'rejected' || $request['status'] === 'disapproved') ? 'red' : 
                           (($request['status'] === 'issued') ? 'gray' : 'gray')));
            echo "<td>" . (!empty($request['remarks']) ? (strlen($request['remarks']) > 16 ? htmlspecialchars(substr($request['remarks'], 0, 16)) . "..." : htmlspecialchars($request['remarks'])) : "N/A") . "</td>";
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
                             1px 1px 0 black; /* Creates an outline effect */
            '>" . htmlspecialchars($request['status']) . "</span></td>";

            echo "<td>"; 
            echo '<div style="display: flex; gap: 10px;">';
            if ($request['status'] === 'pending') {
                echo '
                    <button class="approve-button buttons" 
                        style="background-color: #008000; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                        onclick="showApprovePopup(\'' . htmlspecialchars($request['request_id'], ENT_QUOTES, 'UTF-8') . '\')">
                        Approve
                    </button>
                    <button class="disapprove-button buttons" 
                        style="background-color: #ff0707; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                        onclick="showDisapprovePopup(\'' . htmlspecialchars($request['request_id'], ENT_QUOTES, 'UTF-8') . '\')">
                        Disapprove
                    </button>';
            } elseif ($request['status'] === 'approved') {
                echo '
                    <button class="issue-button buttons" 
                        style="background-color: #008000; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-weight: bold; text-shadow: -1px -1px 0 black, 1px -1px 0 black, -1px 1px 0 black, 1px 1px 0 black;"
                        onclick="showIssuePopup(\'' . htmlspecialchars($request['request_id'], ENT_QUOTES, 'UTF-8') . '\')">
                        Issue
                    </button>';
            }
            
            echo '<button class="modal-open button" style="background-color:rgb(92, 92, 92)"; aria-haspopup="true" 
            data-request-id="' . $request['request_id'] . '" 
            data-document-type="' . htmlspecialchars($request['document_name']) . '"
            data-name-requested="' . htmlspecialchars($request['name_requested']) . '"
            data-purpose="' . htmlspecialchars($request['purpose']) . '"
            data-date-submitted="' . htmlspecialchars($request['date_submitted']) . '"
            data-payment-type-id="' . htmlspecialchars($request['payment_type_id']) . '" 
            data-proof-of-payment="' . htmlspecialchars($request['proof_of_payment']) . '" 
            data-payment-amount="' . htmlspecialchars($request['payment_amount']) . '"
            data-no-of-copies="' . htmlspecialchars($request['no_of_copies']) . '"
            data-status="' . htmlspecialchars($request['status']) . '"
            data-remarks="' . htmlspecialchars($request['remarks']) . '"
            data-price="' . htmlspecialchars($request['template_price']) . '"
            onclick="populateModal(this)">
            <i class="fa-solid fa-eye"></i>
        </button>';
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='14'>No requests found.</td></tr>";
    }
}