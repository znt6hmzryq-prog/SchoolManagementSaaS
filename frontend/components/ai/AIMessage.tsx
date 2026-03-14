"use client";

import React from 'react';

type Props = {
  role: 'teacher' | 'ai';
  text: string;
};

export default function AIMessage({ role, text }: Props) {
  const isTeacher = role === 'teacher';
  return (
    <div className={`flex ${isTeacher ? 'justify-end' : 'justify-start'} mb-3`}> 
      <div className={`max-w-[80%] p-3 rounded-lg ${isTeacher ? 'bg-blue-600 text-white rounded-br-none' : 'bg-gray-800 text-gray-100 rounded-bl-none'}`}>
        <div className="whitespace-pre-wrap text-sm">{text}</div>
      </div>
    </div>
  );
}
