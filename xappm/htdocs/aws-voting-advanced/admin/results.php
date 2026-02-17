<?php
include("../config/database.php");

$current_time = date("Y-m-d H:i:s");

$elections = mysqli_query($conn,"SELECT * FROM elections");

while($election = mysqli_fetch_assoc($elections)) {

    echo "<h2>".$election['title']."</h2>";

    $election_id = $election['id'];

    // Get vote counts
    $result = mysqli_query($conn,"
    SELECT candidates.id, candidates.name,
    COUNT(votes.id) as total_votes
    FROM candidates
    LEFT JOIN votes ON candidates.id = votes.candidate_id
    WHERE candidates.election_id = $election_id
    GROUP BY candidates.id
    ORDER BY total_votes DESC
    ");

    $winner_id = null;
    $highest_votes = 0;

    // First pass to find winner
    $temp = [];
    while($row = mysqli_fetch_assoc($result)){
        $temp[] = $row;

        if($row['total_votes'] > $highest_votes){
            $highest_votes = $row['total_votes'];
            $winner_id = $row['id'];
        }
    }

    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
            <th>Candidate</th>
            <th>Total Votes</th>
            <th>Status</th>
          </tr>";

    foreach($temp as $row){

        echo "<tr>";

        echo "<td>".$row['name']."</td>";
        echo "<td>".$row['total_votes']."</td>";

        if($election['end_date'] < $current_time && 
           $row['id'] == $winner_id){

            echo "<td style='color:green;font-weight:bold;'>
                    🏆 WINNER
                  </td>";

        } else {
            echo "<td>-</td>";
        }

        echo "</tr>";
    }

    echo "</table><br><br>";
}
?>
