#!/bin/bash

# Launch Claude Code session in /tmp/mcp-test with a temporary MCP config in order
# to test the Automad MCP server without any context such as the code base.

. ./.env
export AM_MCP_SERVER_TOKEN

MCP_TMP=/tmp/mcp-test
mkdir -p "$MCP_TMP"
cd "$MCP_TMP"

cat >.mcp.json <<'EOF'
{
	"mcpServers": {
		"automad": {
			"type": "http",
			"url": "http://localhost:8000/mcp",
			"headers": {
				"Authorization": "Bearer ${AM_MCP_SERVER_TOKEN}"
			}
		}
	}
}
EOF

claude --mcp-config .mcp.json
