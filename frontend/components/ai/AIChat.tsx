"use client";

import React, { useState, useRef, useEffect } from 'react';
import { useMutation } from '@tanstack/react-query';
import api from '../../services/api';
import AIMessage from './AIMessage';

type Message = { id: string; role: 'teacher' | 'ai'; text: string };

const QUICK_ACTIONS: { label: string; prompt: string }[] = [
  { label: 'Generate Quiz', prompt: 'Generate a 10-question multiple choice quiz on {topic}.' },
  { label: 'Lesson Plan', prompt: 'Create a lesson plan for a 45-minute class covering {topic}.' },
  { label: 'Homework Ideas', prompt: 'Provide homework ideas and practice problems for {topic}.' },
  { label: 'Analyze Class', prompt: 'Analyze class performance and suggest areas to improve.' },
];

export default function AIChat({ classId }: { classId?: number }) {
  const [messages, setMessages] = useState<Message[]>([]);
  const [input, setInput] = useState('');
  const [loadingText, setLoadingText] = useState(false);
  const bottomRef = useRef<HTMLDivElement | null>(null);

  const mutation = useMutation(async (payload: any) => {
    const res = await api.post('/ai/teacher-assistant', payload);
    return res.data;
  });

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages, loadingText]);

  const sendMessage = async (prefill?: string) => {
    const prompt = prefill ?? input.trim();
    if (!prompt) return;

    const teacherMsg: Message = { id: Date.now().toString(), role: 'teacher', text: prompt };
    setMessages((m) => [...m, teacherMsg]);
    setInput('');
    setLoadingText(true);

    try {
      const payload: any = { prompt };
      if (classId) payload.class_id = classId;

      const result = await mutation.mutateAsync(payload);
      const aiText = result?.answer || 'No response from AI.';
      const aiMsg: Message = { id: (Date.now() + 1).toString(), role: 'ai', text: aiText };
      setMessages((m) => [...m, aiMsg]);
    } catch (err: any) {
      const aiMsg: Message = { id: (Date.now() + 2).toString(), role: 'ai', text: 'Error: ' + (err?.message ?? 'Request failed') };
      setMessages((m) => [...m, aiMsg]);
    } finally {
      setLoadingText(false);
    }
  };

  return (
    <div className="flex flex-col h-full bg-gray-900 text-gray-100 rounded-lg p-4">
      <div className="flex gap-2 mb-3">
        {QUICK_ACTIONS.map((a) => (
          <button
            key={a.label}
            onClick={() => sendMessage(a.prompt.replace('{topic}', 'your topic here'))}
            className="bg-gray-800 hover:bg-gray-700 px-3 py-1 rounded text-sm"
          >
            {a.label}
          </button>
        ))}
      </div>

      <div className="flex-1 overflow-auto mb-3" style={{ maxHeight: '60vh' }}>
        {messages.map((m) => (
          <AIMessage key={m.id} role={m.role} text={m.text} />
        ))}
        {loadingText && (
          <div className="text-sm text-gray-400">AI is thinking...</div>
        )}
        <div ref={bottomRef} />
      </div>

      <div className="mt-2">
        <textarea
          value={input}
          onChange={(e) => setInput(e.target.value)}
          rows={3}
          className="w-full p-2 rounded bg-gray-800 text-gray-100 resize-none"
          placeholder="Ask the assistant..."
        />
        <div className="flex items-center justify-end mt-2">
          <button
            onClick={() => sendMessage()}
            disabled={loadingText}
            className="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded"
          >
            {loadingText ? 'Sending...' : 'Send'}
          </button>
        </div>
      </div>
    </div>
  );
}
