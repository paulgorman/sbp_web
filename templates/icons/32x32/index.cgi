#!/usr/bin/perl
##use Image::Info qw(image_info dim);

print qq(Content-type: text/html

        <html>
        <body bgcolor="#FFFFFF" text="#3f3f3f" link="#FFFF00" alink="#FF0000" vlink="#FFFF00">
         <center><h3>
          Images
         </h3>
         <a href="#" onClick="window.parent.close()">Close Window</a><br>
         </center>
         <br>);


opendir(DIR,".");
while ($file = readdir(DIR)) {
        next if ($file =~ /^\./);
        next if ($file =~ /index.cgi/);
        if ($file =~ /^top$/) {
                $top = 1;
        }
}
print qq(<center><a href="..">Up One Directory</a></center><br>) if (!$top);

opendir(DIR,".");
while ($file = readdir(DIR)) {
        next if ($file =~ /^\./);
        next if ($file =~ /index.cgi/);
        next if ($file =~ /^top$/);
        if ($file !~ /jpg|gif|png/i) {
                print qq(<ul><li><a href="$file">$file</a><br></ul>\n);
                next;
        }
}
opendir(DIR,".");
while ($file = readdir(DIR)) {
        next if ($file =~ /^\./);
        next if ($file =~ /index.cgi/);
        next if ($file =~ /^top$/);
        next if ($file =~ /map/i);
        next if ($file !~ /jpg|gif|png/i);
        #my $info = image_info("$file");
        #my($w, $h) = dim($info);
        $src = $file;
        $src =~ s/\s/%20/g;
        $copyname =~ s/\s/%20/g;
        print qq(
					<img src="$src" alt="$file"><br><B>$file</B><br><br>
				);
}
print qq(
   	</body>
   	</html>
);
