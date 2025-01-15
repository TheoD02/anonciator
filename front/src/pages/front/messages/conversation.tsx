import { Group, Box, Paper, TextInput, Stack, Avatar, Button, ScrollArea, Text } from "@mantine/core"
import { useCreate, useList } from "@refinedev/core";
import { IconDots, IconDotsVertical, IconMessage, IconPhone, IconSearch, IconSend, IconVideo } from "@tabler/icons-react";
import { useEffect, useRef, useState } from "react";
import { useParams } from "react-router";
import { TOKEN_KEY } from "../../../authProvider";

interface Message {
  id: number;
  text: string;
  sender: 'user' | 'other';
  timestamp: Date;
}

export const FrontMessageConversation = () => {
  const { id } = useParams();
  const { data: messages, isFetching: isMessagesFetching } = useList({
    resource: `conversations/${id}/messages`,
    queryOptions: {
      enabled: true,
      select: (originalData) => {
        return {
          ...originalData,
          data: originalData.data.sort((a, b) => new Date(b.sentAt).getTime() + new Date(a.sentAt).getTime())
        }
      }
    },
  });

  useEffect(() => {
    if (messages && viewport.current) {
      viewport.current.scrollTo({ top: viewport.current.scrollHeight });
    }
  }, [messages]);

  const [newMessage, setNewMessage] = useState('');

  const { mutate: sendMessage } = useCreate({
    resource: `conversations/${id}/messages`,
    values: {
      content: newMessage
    },
  });

  const token = localStorage.getItem(TOKEN_KEY);
  const decodedToken = JSON.parse(atob(token.split('.')[1]));
  const email = decodedToken.email;

  const viewport = useRef<HTMLDivElement>(null);
  return <Paper>
    <Group noWrap sx={{ height: '80vh' }}>
      {/* Sidebar */}
      <Box sx={{ width: 300, height: '100%', borderRight: '1px solid #eee' }}>
        <Paper shadow="xs" p="md">
          <TextInput
            placeholder="Search conversations..."
            icon={<IconSearch size={16} />}
            mb="md"
          />
          <Stack spacing="sm">
            {['Alice Smith', 'Bob Johnson', 'Carol Williams'].map((name, index) => (
              <Paper
                key={index}
                p="sm"
                sx={{ cursor: 'pointer', '&:hover': { backgroundColor: '#f8f9fa' } }}
              >
                <Group>
                  <Avatar color="blue" radius="xl">
                    <IconMessage size={20} />
                  </Avatar>
                  <Box>
                    <Text weight={500}>{name}</Text>
                    <Text size="sm" color="dimmed">Last message...</Text>
                  </Box>
                </Group>
              </Paper>
            ))}
          </Stack>
        </Paper>
      </Box>

      {/* Main Chat Area */}
      <Box sx={{ flex: 1, display: 'flex', flexDirection: 'column', height: '100%' }}>
        {/* Chat Header */}
        <Paper p="md" radius={0} sx={{ borderBottom: '1px solid #eee' }}>
          <Group position="apart">
            <Group>
              <Avatar color="blue" radius="xl">
                <IconMessage size={20} />
              </Avatar>
              <Box>
                <Text weight={500}>Alice Smith</Text>
                <Text size="sm" color="dimmed">Online</Text>
              </Box>
            </Group>
            <Group>
              <Button variant="subtle" size="sm" px={8}>
                <IconPhone size={20} />
              </Button>
              <Button variant="subtle" size="sm" px={8}>
                <IconVideo size={20} />
              </Button>
              <Button variant="subtle" size="sm" px={8}>
                <IconDotsVertical size={20} />
              </Button>
            </Group>
          </Group>
        </Paper>

        {/* Messages */}
        <ScrollArea sx={{ flex: 1 }} p="md" viewportRef={viewport}>
          <Stack spacing="md">
            {messages?.data.map((message) => (
              <Box
                key={message.id}
                sx={{
                  display: 'flex',
                  justifyContent: message.sentBy === email ? 'flex-end' : 'flex-start'
                }}
              >
                <Paper
                  p="sm"
                  radius="md"
                  sx={(theme) => ({
                    backgroundColor: message.sentBy === email ? theme.colors.blue[6] : theme.colors.gray[0],
                    color: message.sentBy === email ? 'white' : 'inherit',
                    maxWidth: '70%'
                  })}
                >
                  <Text>{message.content}</Text>
                  <Text size="xs" color={message.sentBy === email ? 'gray.2' : 'dimmed'} mt={4}>
                    {new Date(message.sentAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                  </Text>
                </Paper>
              </Box>
            ))}
          </Stack>
        </ScrollArea>

        {/* Conversation Input */}
        <Paper p="md" radius={0} sx={{ borderTop: '1px solid #eee' }}>
          <form onSubmit={(e) => {
            e.preventDefault();
            sendMessage();
          }}>
            <Group grow>
              <TextInput
                placeholder="Type a message..."
                value={newMessage}
                onChange={(e) => setNewMessage(e.target.value)}
              />
              <Button type="submit" rightIcon={<IconSend size={16} />} sx={{ width: 'auto' }}>
                Send
              </Button>
            </Group>
          </form>
        </Paper>
      </Box>
    </Group>
  </Paper>
}
