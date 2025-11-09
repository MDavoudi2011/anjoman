
import React, { useState, useEffect } from 'react';
import type { User } from '../types';
import { api } from '../services/api';

interface ProfileViewProps {
    user: User;
}

const ProfileView: React.FC<ProfileViewProps> = ({ user }) => {
    const [totalVideos, setTotalVideos] = useState(0);
    const [isLoading, setIsLoading] = useState(true);
    
    useEffect(() => {
        const fetchContent = async () => {
            setIsLoading(true);
            const content = await api.getTrainingContent(user.group);
            const count = content.reduce((sum, category) => sum + category.videos.length, 0);
            setTotalVideos(count);
            setIsLoading(false);
        };
        fetchContent();
    }, [user.group]);

    const watchedCount = user.watchedVideos.length;
    const completionPercentage = totalVideos > 0 ? Math.round((watchedCount / totalVideos) * 100) : 0;

    if (isLoading) {
        return <div className="text-center">در حال بارگذاری پروفایل...</div>;
    }

    return (
        <div className="p-6 space-y-8 bg-white rounded-lg shadow-lg dark:bg-gray-800">
            <h2 className="pb-2 text-3xl font-extrabold text-gray-800 border-b-4 border-indigo-500 dark:text-white">پروفایل کاربری</h2>
            
            <div className="p-6 border border-gray-200 rounded-lg dark:border-gray-700">
                <p className="text-lg"><span className="font-bold">نام کاربری:</span> {user.username}</p>
                <p className="text-lg"><span className="font-bold">گروه تخصیص یافته:</span> گروه {user.group}</p>
            </div>

            <div>
                <h3 className="mb-4 text-2xl font-bold text-gray-700 dark:text-gray-300">میزان پیشرفت</h3>
                <div className="space-y-3">
                    <div className="flex justify-between mb-1">
                        <span className="text-base font-medium text-indigo-700 dark:text-white">پیشرفت دوره‌ها</span>
                        <span className="text-sm font-medium text-indigo-700 dark:text-white">{watchedCount} از {totalVideos} ویدئو</span>
                    </div>
                    <div className="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                        <div 
                            className="bg-indigo-600 h-4 rounded-full transition-all duration-500 ease-out" 
                            style={{ width: `${completionPercentage}%` }}
                        ></div>
                    </div>
                     <p className="text-center text-indigo-600 dark:text-indigo-400">{completionPercentage}% تکمیل شده</p>
                </div>
            </div>
        </div>
    );
};

export default ProfileView;
