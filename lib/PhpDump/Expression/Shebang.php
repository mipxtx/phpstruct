<?php
/**
 * @author: mix
 * @date: 23.12.14
 */

namespace PhpDump\Expression;


use PhpDump\BaseDump;

class Shebang extends BaseDump{
    /**
     * @param \PhpStruct\Expression\Shebang $in
     * @param int $level
     * @return string
     */
    public function process($in, $level) {
        return "#!/usr/bin/env php";
    }
}