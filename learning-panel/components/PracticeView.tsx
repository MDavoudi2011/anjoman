
import React, { useState, useEffect } from 'react';
import type { User, PracticeChallenge } from '../types';
import { api } from '../services/api';
import Modal from './Modal';

interface PracticeViewProps {
  user: User;
}

const PracticeView: React.FC<PracticeViewProps> = ({ user }) => {
  const [content, setContent] = useState<PracticeChallenge[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [selectedChallenge, setSelectedChallenge] = useState<PracticeChallenge | null>(null);

  useEffect(() => {
    const fetchContent = async () => {
      setIsLoading(true);
      const data = await api.getPracticeContent(user.group);
      setContent(data);
      setIsLoading(false);
    };
    fetchContent();
  }, [user.group]);

  const difficultyColors = {
    'آسان': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'متوسط': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'سخت': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
  };

  if (isLoading) {
    return <div className="text-center">در حال بارگذاری چالش‌ها...</div>;
  }

  return (
    <div className="space-y-8">
      <h2 className="pb-2 text-3xl font-extrabold text-gray-800 border-b-4 border-indigo-500 dark:text-white">چالش‌های تمرینی</h2>
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {content.map((challenge) => (
          <div key={challenge.id} className="flex flex-col justify-between p-6 bg-white rounded-lg shadow-lg dark:bg-gray-800">
            <div>
              <div className="flex items-start justify-between mb-2">
                <h3 className="text-xl font-bold text-gray-800 dark:text-white">{challenge.title}</h3>
                <span className={`px-2.5 py-1 text-xs font-semibold rounded-full ${difficultyColors[challenge.difficulty]}`}>
                  {challenge.difficulty}
                </span>
              </div>
              <p className="text-sm text-gray-600 dark:text-gray-400">
                سطح سختی: {challenge.difficulty}
              </p>
            </div>
            <button
              onClick={() => setSelectedChallenge(challenge)}
              className="w-full px-4 py-2 mt-4 font-semibold text-white transition-colors bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              مشاهده جزئیات
            </button>
          </div>
        ))}
      </div>

      {selectedChallenge && (
        <Modal
          title={selectedChallenge.title}
          onClose={() => setSelectedChallenge(null)}
        >
          <div className="space-y-4 text-gray-600 dark:text-gray-300">
            <p>{selectedChallenge.description}</p>
            <button className="w-full px-4 py-2 font-semibold text-white bg-gray-500 rounded-md cursor-not-allowed hover:bg-gray-600 focus:outline-none">
              آپلود کد (غیرفعال)
            </button>
          </div>
        </Modal>
      )}
    </div>
  );
};

export default PracticeView;
