import { Group, Box, Paper, TextInput, Stack, Avatar, Button, ScrollArea, Text } from "@mantine/core"
import { IconDots, IconDotsVertical, IconMessage, IconPhone, IconSearch, IconSend, IconVideo } from "@tabler/icons-react";
import { useState } from "react";

interface Message {
    id: number;
    text: string;
    sender: 'user' | 'other';
    timestamp: Date;
}

export const FrontMessageConversation = () => {
    const [messages, setMessages] = useState<Message[]>([
        { id: 1, text: "Hey! How are you?", sender: 'other', timestamp: new Date(Date.now() - 3600000) },
        { id: 2, text: "I'm doing great! Just working on some new projects.", sender: 'user', timestamp: new Date(Date.now() - 1800000) },
        { id: 3, text: "That sounds exciting! What are you building?", sender: 'other', timestamp: new Date(Date.now() - 900000) },
    ]);
    const [newMessage, setNewMessage] = useState('');

    const sendMessage = (e: React.FormEvent) => {
        e.preventDefault();
        if (newMessage.trim()) {
            setMessages([...messages, {
                id: messages.length + 1,
                text: newMessage,
                sender: 'user',
                timestamp: new Date()
            }]);
            setNewMessage('');
        }
    };

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
                <ScrollArea sx={{ flex: 1 }} p="md">
                    <Stack spacing="md">
                        {messages.map((message) => (
                            <Box
                                key={message.id}
                                sx={{
                                    display: 'flex',
                                    justifyContent: message.sender === 'user' ? 'flex-end' : 'flex-start'
                                }}
                            >
                                <Paper
                                    p="sm"
                                    radius="md"
                                    sx={(theme) => ({
                                        backgroundColor: message.sender === 'user' ? theme.colors.blue[6] : theme.colors.gray[0],
                                        color: message.sender === 'user' ? 'white' : 'inherit',
                                        maxWidth: '70%'
                                    })}
                                >
                                    <Text>{message.text}</Text>
                                    <Text size="xs" color={message.sender === 'user' ? 'gray.2' : 'dimmed'} mt={4}>
                                        {message.timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                    </Text>
                                </Paper>
                            </Box>
                        ))}
                    </Stack>
                </ScrollArea>

                {/* Message Input */}
                <Paper p="md" radius={0} sx={{ borderTop: '1px solid #eee' }}>
                    <form onSubmit={sendMessage}>
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