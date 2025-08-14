"""
init schema

Revision ID: 202508140001
Revises: 
Create Date: 2025-08-14 00:00:00
"""

from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import postgresql as psql


# revision identifiers, used by Alembic.
revision = '202508140001'
down_revision = None
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        'request_log',
        sa.Column('id', sa.Integer(), primary_key=True, autoincrement=True),
        sa.Column('method', sa.String(), nullable=False),
        sa.Column('path', sa.String(), nullable=False),
        sa.Column('status', sa.Integer(), nullable=False),
    )

    op.create_table(
        'request_audit',
        sa.Column('id', sa.Integer(), primary_key=True, autoincrement=True),
        sa.Column('method', sa.String(), nullable=False),
        sa.Column('path', sa.String(), nullable=False),
        sa.Column('query', sa.String(), nullable=True),
        sa.Column('body', sa.Text(), nullable=True),
        sa.Column('status', sa.Integer(), nullable=False),
        sa.Column('request_headers_json', psql.JSONB(), nullable=True),
        sa.Column('duration_ms', sa.Float(), nullable=True),
        sa.Column('response_body', sa.Text(), nullable=True),
        sa.Column('request_log_id', sa.Integer(), nullable=True),
        sa.Column('created_at', sa.DateTime(timezone=True), server_default=sa.text('now()'), nullable=False),
    )
    op.create_index('idx_request_audit_created_at', 'request_audit', ['created_at'])
    op.create_index('idx_request_audit_request_log_id', 'request_audit', ['request_log_id'])
    op.create_foreign_key(
        'fk_request_audit_request_log_id',
        'request_audit',
        'request_log',
        ['request_log_id'],
        ['id'],
        ondelete='SET NULL',
    )

    op.create_table(
        'proxy_audit',
        sa.Column('id', sa.Integer(), primary_key=True, autoincrement=True),
        sa.Column('target_url', sa.String(), nullable=False),
        sa.Column('response_body', sa.Text(), nullable=True),
        sa.Column('status', sa.Integer(), nullable=False),
        sa.Column('response_headers_json', psql.JSONB(), nullable=True),
        sa.Column('response_body_json', psql.JSONB(), nullable=True),
        sa.Column('duration_ms', sa.Float(), nullable=True),
        sa.Column('created_at', sa.DateTime(timezone=True), server_default=sa.text('now()'), nullable=False),
    )
    op.create_index('idx_proxy_audit_created_at', 'proxy_audit', ['created_at'])

    op.create_table(
        'tg_message',
        sa.Column('id', sa.Integer(), primary_key=True, autoincrement=True),
        sa.Column('chat_id', sa.BigInteger(), nullable=False),
        sa.Column('user_id', sa.BigInteger(), nullable=True),
        sa.Column('text', sa.Text(), nullable=True),
        sa.Column('date_ts', sa.BigInteger(), nullable=True),
        sa.Column('raw_json', psql.JSONB(), nullable=True),
        sa.Column('created_at', sa.DateTime(timezone=True), server_default=sa.text('now()'), nullable=False),
    )
    op.create_index('idx_tg_message_created_at', 'tg_message', ['created_at'])
    op.create_index('idx_tg_message_chat_id', 'tg_message', ['chat_id'])
    op.create_index('idx_tg_message_user_id', 'tg_message', ['user_id'])


def downgrade() -> None:
    op.drop_index('idx_tg_message_user_id', table_name='tg_message')
    op.drop_index('idx_tg_message_chat_id', table_name='tg_message')
    op.drop_index('idx_tg_message_created_at', table_name='tg_message')
    op.drop_table('tg_message')

    op.drop_index('idx_proxy_audit_created_at', table_name='proxy_audit')
    op.drop_table('proxy_audit')

    op.drop_constraint('fk_request_audit_request_log_id', 'request_audit', type_='foreignkey')
    op.drop_index('idx_request_audit_request_log_id', table_name='request_audit')
    op.drop_index('idx_request_audit_created_at', table_name='request_audit')
    op.drop_table('request_audit')

    op.drop_table('request_log')


