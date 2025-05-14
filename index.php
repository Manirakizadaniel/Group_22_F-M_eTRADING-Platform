<?php
require_once 'Menu.php';
require_once 'Util.php';

try {
$sessionId   = $_POST["sessionId"] ?? '';
$serviceCode = $_POST["serviceCode"] ?? '';
$phoneNumber = $_POST["phoneNumber"] ?? '';
$text        = $_POST["text"] ?? '';


    $textArray = explode("*", $text);

    // Middleware - Create Menu object
    $menu = new Menu($text, $sessionId, $phoneNumber, $conn);

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE phone_number = ?");
    $stmt->execute([$phoneNumber]);
    $user = $stmt->fetch();

    if (!$user) {
        // New/unregistered user flow
        if ($text == "") {
            $menu->mainMenuUnregistered(); // Show: Register | Contact Us
        } else {
            if ($textArray[0] == "1") {
                $menu->menuRegister($textArray);
            }  else {
                echo "END Invalid option. Please try again.";
            }
        }

    } else {
        // Registered user flow
        if ($text == "") {
            $menu->mainMenuRegistered(); // Show: Buyer | Seller | Back
        } else {
            switch ($textArray[0]) {
                case "1":
                    $menu->menuBuyerServices($textArray); // You can implement this later
                    break;

                case "2":
                    $menu->menuSellerServices($textArray); // This is your seller menu
                    break;

                case "3":
                    $menu->mainMenuRegistered(); // Go back to main
                    break;

                default:
                    echo "END Invalid option.";
                    break;
            }
        }
    }

} catch (Exception $e) {
    echo "END An error occurred. Please try again.";
}
?>
