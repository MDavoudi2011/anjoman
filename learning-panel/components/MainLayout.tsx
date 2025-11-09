
import React, { useState } from 'react';
import type { User } from '../types';
import { View } from '../types';
import TrainingView from './TrainingView';
import PracticeView from './PracticeView';
import TestView from './TestView';
import ProfileView from './ProfileView';
import BottomNav from './BottomNav';
import UserCircleIcon from './icons/UserCircleIcon';

interface MainLayoutProps {
  user: User;
  onLogout: () => void;
  updateUser: (user: User) => void;
}

const MainLayout: React.FC<MainLayoutProps> = ({ user, onLogout, updateUser }) => {
  const [activeView, setActiveView] = useState<View>(View.Training);

  const renderView = () => {
    switch (activeView) {
      case View.Training:
        return <TrainingView user={user} updateUser={updateUser} />;
      case View.Practice:
        return <PracticeView user={user} />;
      case View.Test:
        return <TestView user={user} />;
      case View.Profile:
        return <ProfileView user={user} />;
      default:
        return <TrainingView user={user} updateUser={updateUser} />;
    }
  };

  return (
    <div className="flex flex-col min-h-screen font-sans text-gray-900 bg-gray-100 dark:bg-gray-900 dark:text-gray-200">
      <header className="fixed top-0 left-0 right-0 z-20 flex items-center justify-between p-4 bg-white shadow-md dark:bg-gray-800">
        <h1 className="text-xl font-bold text-indigo-600 dark:text-indigo-400">
          پلتفرم آموزشی
        </h1>
        <div className="flex items-center space-x-4">
          <div className="flex items-center space-x-2">
            <span className="text-sm font-medium">
              {user.username} خوش آمدید! (گروه {user.group})
            </span>
            <button
              onClick={() => setActiveView(View.Profile)}
              className="p-2 text-gray-500 rounded-full hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-500"
              aria-label="نمایش پروفایل"
            >
              <UserCircleIcon />
            </button>
          </div>
          <button
            onClick={onLogout}
            className="px-3 py-1.5 text-sm font-semibold text-white bg-red-500 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-red-500"
          >
            خروج
          </button>
        </div>
      </header>
      
      <main className="flex-grow pt-20 pb-24">
        <div className="container p-4 mx-auto">
          {renderView()}
        </div>
      </main>
      
      <BottomNav activeView={activeView} setActiveView={setActiveView} />
    </div>
  );
};

export default MainLayout;
