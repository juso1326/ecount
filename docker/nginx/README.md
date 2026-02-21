# Nginx 萬用字元子網域設定說明

## 本機開發

### 1. 安裝 dnsmasq（一次設定，自動解析所有子網域）
```bash
brew install dnsmasq
echo "address=/.ecount.test/127.0.0.1" | sudo tee -a /usr/local/etc/dnsmasq.conf
sudo mkdir -p /etc/resolver
echo "nameserver 127.0.0.1" | sudo tee /etc/resolver/ecount.test
sudo brew services restart dnsmasq
```

### 2. 套用 Nginx 設定
```bash
# 本機 Nginx
sudo cp docker/nginx/ecount.conf /etc/nginx/sites-available/ecount.conf
sudo ln -s /etc/nginx/sites-available/ecount.conf /etc/nginx/sites-enabled/
sudo nginx -t && sudo nginx -s reload

# Docker（複製到容器內）
docker cp docker/nginx/ecount.conf nginx_container:/etc/nginx/conf.d/ecount.conf
docker exec nginx_container nginx -t && docker exec nginx_container nginx -s reload
```

### 3. 測試
```bash
curl -I http://ecount.test/superadmin/login      # 主網域
curl -I http://abc123.ecount.test/dashboard       # 租戶子網域
```

---

## 生產環境

### DNS（Cloudflare / 任何 DNS）
```
A   ecount.com      →  伺服器 IP
A   *.ecount.com    →  伺服器 IP
```

### Nginx 生產設定
`ecount.conf` 中把 `ecount.test` 全部換成 `ecount.com`，
並在兩個 server block 加入 SSL：
```nginx
listen 443 ssl;
ssl_certificate     /etc/letsencrypt/live/ecount.com/fullchain.pem;
ssl_certificate_key /etc/letsencrypt/live/ecount.com/privkey.pem;
```

### Let's Encrypt 萬用字元憑證
```bash
certbot certonly --dns-cloudflare \
  -d ecount.com \
  -d *.ecount.com
```

---

## .env 對應設定
```env
APP_URL=http://ecount.test
APP_DOMAIN=ecount.test
SESSION_DOMAIN=.ecount.test    # 前面加點，讓 session 跨子網域
```
