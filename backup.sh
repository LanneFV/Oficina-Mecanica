#!/bin/bash

# ─── Configurações ───────────────────────────────────────────────────────────
DB_HOST="127.0.0.1"
DB_USER="root"
DB_PASS="Giulia123!"
DB_NAME="oficina"

BACKUP_DIR="$(dirname "$0")/backups"
LOG_FILE="$BACKUP_DIR/backup.log"
MANTER_ULTIMOS=7   # quantos backups manter

# ─── Garante que a pasta existe ───────────────────────────────────────────────
mkdir -p "$BACKUP_DIR"

# ─── Nome do arquivo com data e hora ─────────────────────────────────────────
TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
ARQUIVO="$BACKUP_DIR/oficina_$TIMESTAMP.sql.gz"

# ─── Executa o backup ────────────────────────────────────────────────────────
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Iniciando backup..." >> "$LOG_FILE"

if [ -z "$DB_PASS" ]; then
    mysqldump -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" | gzip > "$ARQUIVO"
else
    mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$ARQUIVO"
fi

if [ $? -eq 0 ]; then
    TAMANHO=$(du -sh "$ARQUIVO" | cut -f1)
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Backup concluído: $(basename "$ARQUIVO") ($TAMANHO)" >> "$LOG_FILE"
else
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ERRO: falha ao gerar backup." >> "$LOG_FILE"
    exit 1
fi

# ─── Remove backups antigos, mantendo apenas os últimos N ────────────────────
TOTAL=$(ls -1 "$BACKUP_DIR"/oficina_*.sql.gz 2>/dev/null | wc -l)
if [ "$TOTAL" -gt "$MANTER_ULTIMOS" ]; then
    REMOVER=$((TOTAL - MANTER_ULTIMOS))
    ls -1t "$BACKUP_DIR"/oficina_*.sql.gz | tail -n "$REMOVER" | xargs rm -f
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $REMOVER backup(s) antigo(s) removido(s)." >> "$LOG_FILE"
fi

echo "─────────────────────────────────────────────" >> "$LOG_FILE"
