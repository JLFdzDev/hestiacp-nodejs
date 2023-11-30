server {
        listen %ip%:%proxy_port%;
        server_name %domain_idn% %alias_idn%;
        error_log /var/log/%web_system%/domains/%domain%.error.log error;

        include %home%/%user%/hestiacp_nodejs_config/web/%domain%/nodejs-app.conf;

        location /error/ {
        	alias %home%/%user%/web/%domain%/document_errors/;
        }

        include %home%/%user%/hestiacp_nodejs_config/web/%domain%/nodejs-app-fallback.conf;

        location ~ /\.ht {return 404;}
        location ~ /\.svn/ {return 404;}
        location ~ /\.git/ {return 404;}
        location ~ /\.hg/ {return 404;}
        location ~ /\.bzr/ {return 404;}
}