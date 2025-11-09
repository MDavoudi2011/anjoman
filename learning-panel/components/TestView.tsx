
import React, { useState, useEffect } from 'react';
import type { User, TestData } from '../types';
import { api } from '../services/api';

interface TestViewProps {
  user: User;
}

const TestView: React.FC<TestViewProps> = ({ user }) => {
  const [testData, setTestData] = useState<TestData | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    const fetchContent = async () => {
      setIsLoading(true);
      const data = await api.getTestData(user.group);
      setTestData(data);
      setIsLoading(false);
    };
    fetchContent();
  }, [user.group]);

  if (isLoading) {
    return <div className="text-center">در حال بارگذاری آزمون‌ها...</div>;
  }

  if (!testData) {
    return (
        <div className="flex flex-col items-center justify-center h-full p-8 text-center bg-white rounded-lg shadow-lg dark:bg-gray-800">
            <h2 className="text-3xl font-bold text-gray-800 dark:text-white">خطا</h2>
            <p className="mt-4 text-lg text-gray-600 dark:text-gray-400">
                محتوای آزمون یافت نشد.
            </p>
      </div>
    );
  }

  return (
    <div className="flex flex-col items-center justify-center h-full p-8 text-center bg-white rounded-lg shadow-lg dark:bg-gray-800">
      <h2 className="text-3xl font-bold text-gray-800 dark:text-white">{testData.title}</h2>
      <p className="mt-4 text-lg text-gray-600 dark:text-gray-400">
        {testData.message}
      </p>
    </div>
  );
};

export default TestView;
