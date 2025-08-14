"""
drop tg_message table (move Telegram domain data out of HTTP service)

Revision ID: 202508140002
Revises: 202508140001
Create Date: 2025-08-14 15:50:00
"""

from alembic import op


# revision identifiers, used by Alembic.
revision = '202508140002'
down_revision = '202508140001'
branch_labels = None
depends_on = None


def upgrade() -> None:
    # безопасно удаляем индексы и таблицу, если существуют
    op.execute("DROP INDEX IF EXISTS idx_tg_message_user_id;")
    op.execute("DROP INDEX IF EXISTS idx_tg_message_chat_id;")
    op.execute("DROP INDEX IF EXISTS idx_tg_message_created_at;")
    op.execute("DROP TABLE IF EXISTS tg_message CASCADE;")


def downgrade() -> None:
    # откат: не создаём заново доменно-специфичную таблицу
    pass


