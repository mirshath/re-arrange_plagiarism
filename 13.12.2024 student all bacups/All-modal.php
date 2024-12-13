
<!-- Modal for Invalid Student ID or DOB -->
<div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center modal-background">
                <!-- Red Alert Icon (Bootstrap Icons) -->
                <i class="bi bi-exclamation-circle-fill" style="font-size: 50px; color: red;"></i>
                <h4 class="eb-garamond fw-bolder" style="font-size: 35px;">Invalid Credentials</h4>
                <p>Please verify the <b>Student ID</b> and <b>Date of Birth</b>, and try again.</p>
                <p>If you require further assistance, please contact our coordinator.</p>
                <!-- <p class="fw-bolder">Name here</p> -->
                <!-- <p style="margin-top: -15px;"><a href="mailto:ali@bms.ac.lk" style="text-decoration: none;">ali@bms.ac.lk</a></p> -->

                <button class="btn btn-primary" data-bs-dismiss="modal">Go Back</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Registration Success -->
<div id="successModal" class="modal fade" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header">
                <!-- Close button -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center modal-background">
                <!-- Success Icon (Bootstrap Icons) -->
                <i class="bi bi-check-circle-fill" style="font-size: 50px; color: green;"></i>
                <h4 class="eb-garamond fw-bolder" style="font-size: 35px;">Thank you for completing the Document</h4>
                <!-- <p>Your feedback is valuable in helping us improve our services and enhance your experience. If you have any further questions or suggestions, feel free to reach out to us. We appreciate your time and input!</p> -->
                <!-- 'Go Back' button to close the modal -->
                <button class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<!-- Error Modal for Missing Fields -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-background" class="eb-garamond fw-bolder" style="font-size: 35px;">
                <!-- The error message will be inserted here dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
