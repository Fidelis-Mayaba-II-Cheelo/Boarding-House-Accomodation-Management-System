<?php
include('db-connect.php');
include_once('../Admin/functions.php');
include('../Admin/session_handler.php');

$hostel = sanitize_input($_POST['hostel']);

// Define the maximum bedspaces per room based on hostel type
$bedspaces_per_room = [
    'Single' => 1,
    'Double' => 2,
    'Triple' => 3,
    'Quadruple' => 4
];

$max_bedspaces = $bedspaces_per_room[$hostel] ?? 4; // Default to 4 if hostel type not recognized

// Prepare SQL statement to fetch all rooms and bedspaces for the selected hostel
$sql = "SELECT room_number, GROUP_CONCAT(bedspace_number) as bedspaces 
        FROM students 
        WHERE hostel = ? 
        GROUP BY room_number";

$stmt = $mysqli->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $hostel);  
    $stmt->execute();                 
    $result = $stmt->get_result();    

    $available_rooms = [];
    for ($room = 1; $room <= 15; $room++) { 
        $occupied_bedspaces = [];

        // Check if this room is in the results
        while ($row = $result->fetch_assoc()) {
            if ($row['room_number'] == $room) {
                $occupied_bedspaces = explode(',', $row['bedspaces']);
                break;
            }
        }

        $available_bedspaces = array_diff(range(1, $max_bedspaces), $occupied_bedspaces);

        // Add room to available_rooms only if it has available bedspaces
        if (!empty($available_bedspaces)) {
            $available_rooms[] = [
                'room_number' => $room,
                'bedspace_number' => array_values($available_bedspaces)
            ];
        }
    }

    // Return JSON with available rooms and their bedspaces
    echo json_encode(['rooms' => $available_rooms]);

    $stmt->close();  
} else {
    echo json_encode(['error' => 'Database query failed.']);
}

?>


