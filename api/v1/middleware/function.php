<?php
function total_views($conn, $page_id = null)
{
  if ($page_id === null) {
    // count total website views
    $query = "SELECT sum(total_views) as total_views FROM pages";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
      while ($row = $result->fetch_assoc()) {
        if ($row['total_views'] === null) {
          return 0;
        } else {
          return $row['total_views'];
        }
      }
    } else {
      return "No records found!";
    }
  } else {
    // count specific page views
    $query = "SELECT total_views FROM pages WHERE id='$page_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
      while ($row = $result->fetch_assoc()) {
        if ($row['total_views'] === null) {
          return 0;
        } else {
          return $row['total_views'];
        }
      }
    } else {
      return "No records found!";
    }
  }
}

function all_views($conn, $freq = null)
{
  $clause = $freq === null ? "" : "WHERE $freq(createOn) = $freq(CURDATE())";

  $query = "SELECT count(visitor_ip) as total_views_today FROM daily_views $clause";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) > 0) {
    while ($row = $result->fetch_assoc()) {
      if ($row['total_views_today'] === null) {
        return 0;
      } else {
        return $row['total_views_today'];
      }
    }
  } else {
    return "No records found!";
  }
}

function is_unique_view($conn, $visitor_ip, $page_id, $tabel = 'page_views')
{
  $clause = $tabel === 'page_views' ? "page_id=(SELECT id FROM 
  pages WHERE pages.name = '$page_id')" : "DATE(createOn) = CURDATE()";

  $query = "SELECT * FROM $tabel WHERE visitor_ip='$visitor_ip' AND $clause";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) > 0) {
    return false;
  } else {
    return true;
  }
}

function add_view($conn, $visitor_ip, $page_id)
{
  if (is_unique_view($conn, $visitor_ip, $page_id)) {
    // insert unique visitor record for checking whether the visit is unique or not in future.
    $query = "INSERT INTO page_views (visitor_ip, page_id) VALUES ('$visitor_ip', (SELECT id FROM pages WHERE pages.name = '$page_id'))";

    if (mysqli_query($conn, $query)) {
      // At this point unique visitor record is created successfully. Now update total_views of specific page.
      $query = "UPDATE pages SET total_views = total_views + 1 WHERE pages.name = '$page_id'";

      if (!mysqli_query($conn, $query)) {
        echo "Error updating record: " . mysqli_error($conn);
      }
    } else {
      echo "Error inserting record: " . mysqli_error($conn);
    }
  }
}

function add_daily($conn, $visitor_ip)
{
  if (is_unique_view($conn, $visitor_ip, 5, 'daily_views')) {
    // insert unique visitor record for checking whether the visit is unique or not in future.
    $query = "INSERT INTO daily_views (visitor_ip) VALUES ('$visitor_ip')";

    if (!mysqli_query($conn, $query)) {
      echo "Error inserting record: " . mysqli_error($conn);
    }
  }
}

function slugify($text, string $divider = '-')
{
  // replace non letter or digits by divider
  $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, $divider);

  // remove duplicate divider
  $text = preg_replace('~-+~', $divider, $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}