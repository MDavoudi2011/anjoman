
import React, { useState, useCallback } from 'react';
import type { User } from './types';
import { api } from './services/api';
import AuthScreen from './components/AuthScreen';
import MainLayout from './components/MainLayout';

const App: React.FC = () => {
  const [user, setUser] = useState<User | null>(null);

  const handleLogin = async (username: string, password: string): Promise<boolean> => {
    const loggedInUser = await api.login(username, password);
    if (loggedInUser) {
      setUser(loggedInUser);
      return true;
    }
    return false;
  };

  const handleLogout = () => {
    setUser(null);
  };

  const updateUser = useCallback(async (updatedUser: User) => {
    setUser(updatedUser);
    // Persist changes to our simulated backend
    await api.updateUserProgress(updatedUser);
  }, []);

  if (!user) {
    return <AuthScreen onLogin={handleLogin} />;
  }

  return <MainLayout user={user} onLogout={handleLogout} updateUser={updateUser} />;
};

export default App;
