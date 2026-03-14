"use client";

import React from 'react';
import AIChat from '../../../../components/ai/AIChat';

export default function Page() {
  return (
    <div className="p-6">
      <h1 className="text-2xl font-semibold mb-4 text-white">AI Teacher Assistant</h1>
      <p className="text-gray-300 mb-4">Use the assistant to generate quizzes, lesson plans, and analyze student performance.</p>

      <div className="bg-gray-800 p-4 rounded">
        <AIChat />
      </div>
    </div>
  );
}
