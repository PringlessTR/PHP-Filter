<!DOCTYPE html>

<div style="float:left;width:40%;">

<title>Filter Reviews</title>
<h3>Filter Reviews</h3>
</br>
<label> Order by Rating : </label>
</br>
<form action="" method="POST">
<select id="Rating" name="Rating" >
   <option value="0">Highest First</option>
   <option value="1">Lowest First</option>
</select>
</br>
</br>
<label> Minimum Rating : </label>
</br>
<select id="'MRating'" name="MRating" >
   <option value="1">1</option>
   <option value="2">2</option>
   <option value="3">3</option>
   <option value="4">4</option>
   <option value="5">5</option>
</select>

</br>
</br>
<label> Order by Date : </label>
</br>
<select id="ODate" name="ODate" >
   <option value="1">Oldest First</option>
   <option value="2">Newest First</option>
</select>

</br>
</br>
<label> Prioritize by text : </label>
</br>
<select id="PText" name="PText" >
   <option value="1">Yes</option>
   <option value="2">No</option>
</select>
</br>
</br>
   <button type="submit" name="search">Filter</button>
</br>
</form>

<?php
if(isset($_POST["search"])){
$json_data = file_get_contents('reviews.json');
$data = json_decode($json_data, true);
$DS = 'Oldest First';
$TS = 'with';
$orderByRating = $_POST['Rating'] ?? 0; // default is Highest First
$minRating = $_POST['MRating'] ?? 0; // default is 0, which means show all reviews
$orderByDate = $_POST['ODate'] ?? 2; // default is Newest First
$prioritizeByText = $_POST['PText'] ?? 2; // default is No
if ($orderByDate == 2) {
   $DS = 'Newest First';
}


$textReviews = [];
$noTextReviews = [];
foreach ($data as $item) {
   if (!empty($item['reviewFullText'])) {
      $textReviews[] = $item;
   } else {
      $noTextReviews[] = $item;
   }
}

usort($textReviews, function($a, $b) use ($orderByRating, $orderByDate) {
   if ($a['rating'] == $b['rating']) {
      if ($orderByDate == 1) {
         return strtotime($a['reviewCreatedOnDate']) - strtotime($b['reviewCreatedOnDate']); 
      } else {
         return strtotime($b['reviewCreatedOnDate']) - strtotime($a['reviewCreatedOnDate']); 
      }
   } else {
      // Sort by rating
      if ($orderByRating == 1) {
         return $a['rating'] - $b['rating']; 
      } else {
         return $b['rating'] - $a['rating']; 
      }
   }
});


usort($noTextReviews, function($a, $b) use ($orderByRating, $orderByDate) {
   if ($a['rating'] == $b['rating']) {
      if ($orderByDate == 1) {
         return strtotime($a['reviewCreatedOnDate']) - strtotime($b['reviewCreatedOnDate']); 
      } else {
         return strtotime($b['reviewCreatedOnDate']) - strtotime($a['reviewCreatedOnDate']); 
      }
   } else {
      // Sort by rating
      if ($orderByRating == 1) {
         return $a['rating'] - $b['rating']; 
      } else {
         return $b['rating'] - $a['rating']; 
      }
   }
});


if ($prioritizeByText == 1) {
   $sortedReviews = array_merge($textReviews, $noTextReviews);
} else {
   $sortedReviews = array_merge($noTextReviews, $textReviews);
}


if ($minRating > 0) {
   $filteredReviews = array_filter($sortedReviews, function($item) use ($minRating) {
      return $item['rating'] >= $minRating;
   });
} else {
   $filteredReviews = $sortedReviews;
}


foreach ($filteredReviews as $item) { 
if (isset($item['reviewText']) && !empty($item['reviewText'])) {
} else {
    $TS = 'without';
}
    echo '<br>'. $item['rating'] .'-star reviews ' . $TS . ' - ' . $DS .'';
}

echo '<br></div>
<div style="float:right;width:60%;">';
echo '<table border="1">
<tr>
<th>ID</th>
<th>Text</th>
<th>Rating</th>
<th>Date</th>
</tr>';
foreach ($filteredReviews as $item) {
echo '<tr><td>'. $item['id'] .'</td><td>' . $item['reviewFullText'] . '</td><td> ' . $item['rating'] . '</td>' . '<td>' . $item['reviewCreatedOnDate'] . '</td></tr>';
}
echo '</table>';
}
?>
</div>
<div style="clear:both;"></div>
</html>