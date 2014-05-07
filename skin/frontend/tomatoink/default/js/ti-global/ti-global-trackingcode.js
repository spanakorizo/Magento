console.log("global tracking code here");

if (homepage == "true") {console.log("homepage");}
else if (typeof ti_global_pagetype != "undefined" && ti_global_pagetype == "brand") {console.log("brandpage");}
else if (typeof ti_global_pagetype != "undefined" && ti_global_pagetype == "grouped") {console.log("printer model page");}
else if (typeof ti_global_pagetype != "undefined" && ti_global_pagetype == "simple") {console.log("simple product page");}
else if (typeof ti_global_pagetype != "undefined" && ti_global_pagetype == "bundle") {console.log("combopack page");}
else console.log("unknown page");
