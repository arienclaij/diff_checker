<?php
error_reporting(E_ALL & ~E_NOTICE);

function diff_styled($diff) {
        $diff_pattern = "/\*{3}\s([\d,]+)\s\*{4}\s?(.*)-{3}\s([\d,]+)\s-{4}\s?(.*)$/s";
        $line_pattern = "/^[\r\n]?([ \-+!])\s(.*)$/m";
        $result = '';
        $diff = preg_split("/\*{15}/", htmlentities($diff));

        for ($block_nr=1; $block_nr<count($diff); $block_nr++)
        {
            if (!preg_match($diff_pattern, $diff[$block_nr], $block))
                break;

            preg_match_all($line_pattern, $block[2], $new, PREG_SET_ORDER);
            preg_match_all($line_pattern, $block[4], $old, PREG_SET_ORDER);

            $result .= "<pre class=\"diff\"><address>" . diff_lines($block[3], $block[1]) . ":</address>";

            $old_line = $new_line = 0;
            while ($old_line<count($old) || $new_line<count($new))
            {
                $match = ($old[$old_line] ? $old[$old_line][1] : " ") . ($new[$new_line] ? $new[$new_line][1] : " ");

                switch ($match)
                {
                    case "  ":
                    case " ":
                        $result .= ($old[$old_line] ? $old[$old_line][2] : $new[$new_line][2]);
                        $old_line++; $new_line++; break;
                    case "+ ":
                    case "! ":
                        $result .= "<del>" . $old[$old_line][2] . "</del>";
                        $old_line++; break;
                    case " -":
                    case " !":
                        $result .= "<ins>" . $new[$new_line][2] . "</ins>";
                        $new_line++; break;
                    case "!!":
                        $result .= "<del>" . $old[$old_line][2] . "</del>";
                        $result .= "<ins>" . $new[$new_line][2] . "</ins>";
                        $old_line++; $new_line++; break;
                    default:
                        $result .= "Error: Unknown '" . $match . "'\n";
                        $result .= $old[$old_line][2];
                        $result .= $new[$new_line][2];
                        $old_line++; $new_line++;
                }

            }

            $result .= "";

        }

        return $result;

    }

    function diff_lines($lines_old, $lines_new) {
        $lines_old = explode(",", $lines_old);
        $lines_new = explode(",", $lines_new);
        $lines_start = min($lines_old[0], $lines_new[0]);
        $lines_stop = max($lines_old[1], $lines_new[1]);
        $lines_count = ($lines_new[1] - $lines_new[0]) - ($lines_old[1] - $lines_old[0]);

        if (!$lines_stop)
            $result = sprintf("Regel %u", $lines_start);
        else
            $result = sprintf("Regels %u t/m %u", $lines_start, $lines_stop);

        if ($lines_count)
            if ($lines_count == 1 || $lines_count == -1)
                $result .= sprintf(" (%+d regel)", $lines_count);
            else
                $result .= sprintf(" (%+d regels)", $lines_count);

        return $result;

    }
// here we go!
$content = file_get_contents("patch_sample.txt");
$diff = diff_styled($content);
?>
<!DOCTYPE HTML>
<html lang="nl">
<head>
  <meta charset="utf-8">

  <title>Diff Checker</title>
  <link rel="stylesheet" href="diff_reader.css">

</head>

<body>
    <?php echo $diff; ?>
</body>
</html>