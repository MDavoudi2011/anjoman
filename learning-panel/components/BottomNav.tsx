
import React from 'react';
import { View } from '../types';
import BookOpenIcon from './icons/BookOpenIcon';
import ClipboardListIcon from './icons/ClipboardListIcon';
import DocumentTextIcon from './icons/DocumentTextIcon';

interface BottomNavProps {
  activeView: View;
  setActiveView: (view: View) => void;
}

const BottomNav: React.FC<BottomNavProps> = ({ activeView, setActiveView }) => {
  const navItems = [
    { view: View.Practice, label: 'تمرین', icon: <ClipboardListIcon /> },
    { view: View.Training, label: 'آموزش', icon: <BookOpenIcon /> },
    { view: View.Test, label: 'آزمون', icon: <DocumentTextIcon /> },
  ];

  return (
    <nav className="fixed bottom-0 left-0 right-0 z-10 grid grid-cols-3 bg-white border-t border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
      {navItems.map((item) => {
        const isActive = activeView === item.view;
        const activeClasses = 'text-indigo-600 dark:text-indigo-400';
        const inactiveClasses = 'text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400';
        return (
          <button
            key={item.view}
            onClick={() => setActiveView(item.view)}
            className={`flex flex-col items-center justify-center p-3 text-xs font-medium transition-colors duration-200 ease-in-out ${isActive ? activeClasses : inactiveClasses}`}
            aria-current={isActive ? 'page' : undefined}
          >
            {item.icon}
            <span className="mt-1">{item.label}</span>
          </button>
        );
      })}
    </nav>
  );
};

export default BottomNav;
