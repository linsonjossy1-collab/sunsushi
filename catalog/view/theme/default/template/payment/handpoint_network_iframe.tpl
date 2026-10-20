<script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

    <!-- Load the Hosted Payment Page library -->
    <script src="https://commerce-api.handpoint.com/sdk/web/v1/js/hostedforms.min.js"></script>
    <style>
        input[type=submit] {
            background-color: #e97d29;
            border: none;
            color: white;
            width: 200px;
            padding: 5px;
            text-decoration: none;
            margin: 2px 2px;
            cursor: pointer;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #0074cc;
            font-size: 14pt;
            background-color: #e9ecef;
        }

        .card {
            box-shadow:  0.5px 0.5px 0.5px black;
            width: auto;
            height: 350px;
            margin-right: 20px;
            text-align: center;
            font-family: arial;
            background-color: #ffffff;

        }

        .cardIframe {
            box-shadow: 0px 0px 0px black;
            text-align: center;
            font-family: arial;
            background-color: transparent;

        }

        .price {
            color: #25365d;
            font-size: 22px;
        }


        .tab1cards {
            display: flex;
            flex-direction: row;
        }
    </style>
    
<!-- Embedded iFrame-->
            <iframe width="430" height="580" scrolling="yes" frameBorder="1"  
                sandbox='allow-forms allow-popups allow-scripts allow-same-origin allow-modals allow-top-navigation' allow="payment"
                srcdoc='
                                <head>
                                <script src="https://code.jquery.com/jquery-3.4.1.min.js"
                                integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
                                </head>
                                <body>
                                <?php echo $formhtml; ?> <script>
                                        // Create a new Hosted Form object which will cause the above <form> to load into a modal
                                        // overlay over this page.
                                        var form = $(document.forms).submit();
                                    </script></body>'>
            </iframe>