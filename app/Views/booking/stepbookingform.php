<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3-Step Form</title>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <form id="step-form">
        <!-- Step 1 -->
        <div class="step" id="step-1">
            <h2>Step 1</h2>
            <input type="text" name="field1" placeholder="Field 1">
            <button class="next-step">Next</button>
        </div>

        <!-- Step 2 -->
        <div class="step" id="step-2" style="display: none;">
            <h2>Step 2</h2>
            <input type="text" name="field2" placeholder="Field 2">
            <button class="prev-step">Previous</button>
            <button class="next-step">Next</button>
        </div>

        <!-- Step 3 -->
        <div class="step" id="step-3" style="display: none;">
            <h2>Step 3</h2>
            <input type="text" name="field3" placeholder="Field 3">
            <button class="prev-step">Previous</button>
            <button type="submit">Submit</button>
        </div>
    </form>

    <div id="result"></div>

    <script>
        $(document).ready(function () {
            var currentStep = 1;

            // Function to show the current step
            function showStep(step) {
                $('.step').hide();
                $('#step-' + step).show();
            }

            // Next button click event
            $('.next-step').click(function () {
                currentStep++;
                showStep(currentStep);
            });

            // Previous button click event
            $('.prev-step').click(function () {
                currentStep--;
                showStep(currentStep);
            });

            // Form submission using AJAX
            $('#step-form').submit(function (e) {
                e.preventDefault();
                var formData = $(this).serialize();

                // Simulate AJAX request (replace with your actual AJAX call)
                setTimeout(function () {
                    $('#result').html('Form submitted successfully!');
                }, 1000);
            });
        });
    </script>
</body>
</html>
